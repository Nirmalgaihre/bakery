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
        $operator = User::where('role', 'operator')->first();

        if (!$operator) {
            $this->error("No user found with role: operator");
            return;
        }

        $today = Carbon::today();
        $cheques = Cheque::whereNull('email_sent_at')
                         ->where('maturity_date_ad', '<=', $today)
                         ->get();

        if ($cheques->isEmpty()) {
            $this->info("No pending cheques to process.");
            return;
        }

        foreach ($cheques as $cheque) {
            $this->sendEmail($cheque, $operator);
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
            $mail->addAddress($operator->email);

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
            
            $cheque->update(['email_sent_at' => Carbon::now()]);
            $this->info("Sent to {$operator->email}: {$cheque->cheque_no}");
        } catch (Exception $e) {
            $this->error("Failed to send {$cheque->cheque_no}: " . $mail->ErrorInfo);
        }
    }

    public function index(Request $request)
    {
        $cheques = Cheque::query()
            ->when($request->search, function($q, $s) {
                $q->where('cheque_no', 'like', "%$s%")
                ->orWhere('party_name', 'like', "%$s%")
                ->orWhere('bank_name', 'like', "%$s%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Crucial for keeping search term after page clicks

        return view('admin.cheques.index', compact('cheques'));
    }
}