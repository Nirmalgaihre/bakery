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
    protected $description = 'Send email to admin for matured cheques';

    public function handle()
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->error("No users found with the 'admin' role.");
            return;
        }

        $today = Carbon::today();
        
        // Removed ->where('send_reminder', true) as the column does not exist
        $cheques = Cheque::whereNull('email_sent_at')
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
            // Mark as sent
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
            
            // Corrected: Using $operator variable
            $mail->addAddress($operator->email, $operator->name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Reminder: Cheque #{$cheque->cheque_no} Matures Today";

            $mail->Body = "
            Dear {$operator->name},<br><br>
            
            This is an automated reminder from the Deurali Chemical system.<br><br>

            The cheque provided to <b>{$cheque->party_name}</b> is maturing today. Please review the details and take the necessary action to ensure timely processing.<br><br>

            <b>Cheque Details:</b><br><br>

            • Party Name: <b>{$cheque->party_name}</b><br>
            • Cheque Number: <b>#{$cheque->cheque_no}</b><br>
            • Bank Name: <b>{$cheque->bank_name}</b><br>
            • Amount: <b>NPR " . number_format($cheque->amount, 2) . "</b><br>
            • Status: <b>Due Today</b><br><br>

            Regards,<br>
            Deurali Chemical Pvt. Ltd.
            ";

            $mail->send();
            $this->info("Reminder for Cheque #{$cheque->cheque_no} sent to {$operator->email}.");
        } catch (Exception $e) {
            $this->error("Failed to send reminder for Cheque #{$cheque->cheque_no} to {$operator->email}: " . $mail->ErrorInfo);
        }
    }
}