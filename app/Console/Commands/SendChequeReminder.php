<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cheque;
use App\Models\User;
use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once base_path('app/Libraries/PHPMailer/Exception.php');
require_once base_path('app/Libraries/PHPMailer/PHPMailer.php');
require_once base_path('app/Libraries/PHPMailer/SMTP.php');

class SendChequeReminder extends Command
{
    protected $signature = 'cheque:reminder';
    protected $description = 'Send email to operator for matured cheques';

    public function handle()
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->error("No users found with the 'admin' role.");
            return;
        }

        $today = Carbon::today();
        $cheques = Cheque::whereNull('email_sent_at')
                         ->where('send_reminder', true) // Only send if reminder is enabled
                         ->whereDate('maturity_date_ad', $today)
                         ->get();

        if ($cheques->isEmpty()) {
            $this->info("No cheques are due for reminder today.");
            return;
        }

        foreach ($cheques as $cheque) {
            foreach ($admins as $admin) {
                $this->sendEmail($cheque, $admin);
            }
            // Mark as sent after notifying all admins
            $cheque->update(['email_sent_at' => Carbon::now()]);
        }
    }

    private function sendEmail($cheque, $operator)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($admin->email, $admin->name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8'; // नेपाली अक्षरको लागि यो अनिवार्य छ
            $mail->Subject = "महत्वपूर्ण सूचना: चेक #{$cheque->cheque_no} को म्याद पूरा भएको रिमाइन्डर";

            $mail->Body = "
            आदरणीय {$operator->name} ज्यू,<br><br>
            
            यो Deurali Chemical को सिस्टमबाट स्वचालित रूपमा पठाइएको रिमाइन्डर सूचना हो।<br><br>

            तपाईंले <b>{$cheque->party_name}</b> लाई उपलब्ध गराउनुभएको चेकको म्याद आज पूरा भएको जानकारी गराउन चाहन्छौं। 
            उक्त चेकको भुक्तानी तथा आवश्यक प्रक्रियाको लागि कृपया विवरण जाँच गरी आवश्यक कदम चाल्नुहोस्।<br><br>

            <b>चेक सम्बन्धी विवरण:</b><br><br>

            • चेक प्राप्त गर्ने व्यक्ति: <b>{$cheque->party_name}</b><br>
            • चेक नम्बर: <b>#{$cheque->cheque_no}</b><br>
            • बैंकको नाम: <b>{$cheque->bank_name}</b><br>
            • चेक रकम: <b>NPR " . number_format($cheque->amount, 2) . "</b><br>
            • चेक अवस्था: <b>आज म्याद पूरा भएको</b><br><br>

            यो सूचना तपाईंलाई समयमै जानकारी गराउनको लागि पठाइएको हो, ताकि आवश्यक काम समयमा नै सम्पन्न गर्न सकियोस्।<br><br>

            <b>नोट:</b> यो इमेल Deurali Chemical को सिस्टमद्वारा स्वचालित रूपमा (Auto Generated) पठाइएको हो। 
            कृपया यसको जवाफ नदिनुहोस्।<br><br>

            धन्यवाद।<br><br>

            सादर,<br>
            Deurali Chemical Pvt. Ltd.
            ";

            $mail->send();
            $this->info("Reminder for Cheque #{$cheque->cheque_no} sent to {$admin->email}.");
        } catch (Exception $e) {
            $this->error("Failed to send reminder for Cheque #{$cheque->cheque_no} to {$admin->email}: " . $mail->ErrorInfo);
        }
    }
}