<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

// PHPMailer म्यानुअल फाइलहरू लोड गर्ने
require_once base_path('app/Libraries/PHPMailer/Exception.php');
require_once base_path('app/Libraries/PHPMailer/PHPMailer.php');
require_once base_path('app/Libraries/PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendLowStockDigest extends Command
{
    protected $signature = 'inventory:low-stock-digest';
    protected $description = 'Send a daily digest of all low stock products to the admin using PHPMailer.';

    public function handle()
{
    $lowStockProducts = Product::whereRaw('initial_stock <= alert_stock_level')->get();

    if ($lowStockProducts->isEmpty()) {
        $this->info('No low stock items. No email sent.');
        return;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD'); 
        $mail->SMTPSecure = env('MAIL_ENCRYPTION') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = env('MAIL_PORT', 587);
        $mail->CharSet    = 'UTF-8';

        $fromAddress = env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME'));
        $fromName    = env('MAIL_FROM_NAME', 'Deurali Chemical Inventory');
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress('gaihrenirmal2021@gmail.com'); 
        
        $mail->isHTML(true);
       $mail->Subject = 'URGENT: Daily Low Stock Alert - Deurali Chemical';

            $htmlBody = "<h2>⚠️ Low Stock Alert: Action Required</h2>";
            $htmlBody .= "<p>Dear Team,</p>";
            $mail->Body = $htmlBody; // Standard PHPMailer body assignment
            $htmlBody .= "<p>This is an automated daily digest informing you that certain items in the store have fallen below their safe inventory thresholds.</p>";
            $htmlBody .= "<hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>";

            // Example of how to loop through your low stock items in plain text/list format
            // Replace this with your actual database loop
            $htmlBody .= "<p><strong>The following items require immediate reordering:</strong></p>";
            $htmlBody .= "<ul style='line-height: 1.6;'>";
            $htmlBody .= "<li><strong>[Product Name A]</strong> is critically low. Current Stock: <span style='color: red; font-weight: bold;'>[Qty]</span> (Alert Level: [Min Qty])</li>";
            $htmlBody .= "<li><strong>[Product Name B]</strong> is critically low. Current Stock: <span style='color: red; font-weight: bold;'>[Qty]</span> (Alert Level: [Min Qty])</li>";
            $htmlBody .= "</ul>";

            $htmlBody .= "<hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>";
            $htmlBody .= "<p>Please log into the inventory management system to approve the purchase requisitions for these items as soon as possible to prevent stockouts.</p>";
            $htmlBody .= "<p>Best regards,<br><strong>Inventory Control System</strong><br>Deurali Chemical</p>";

        foreach ($lowStockProducts as $product) {
            $htmlBody .= "<tr>
                            <td>{$product->name}</td>
                            <td style='color:red; font-weight:bold;'>{$product->initial_stock} {$product->inventory_unit}</td>
                            <td>{$product->alert_stock_level}</td>
                          </tr>";
        }
        $htmlBody .= "</table>";
        $mail->Body = $htmlBody;

        $mail->send();
        $this->info('Low stock digest sent successfully via PHPMailer.');
        
    } catch (\Exception $e) {
        $this->error("PHPMailer Digest Failed: " . $mail->ErrorInfo);
    }
}
}