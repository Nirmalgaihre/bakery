<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px; }
        .header { border-bottom: 2px solid #d9534f; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { color: #d9534f; margin: 0; font-size: 22px; }
        .details-box { background-color: #f9f9f9; border-left: 4px solid #d9534f; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0; }
        .details-box ul { list-style: none; padding: 0; margin: 0; }
        .details-box li { margin-bottom: 8px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777777; border-top: 1px solid #e0e0e0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Low Stock Notification</h2>
        </div>
        
        <p>Dear Administrator,</p>
        
        <p>This is an automated notification to inform you that the following product has reached or fallen below its designated minimum safety threshold:</p>
        
        <div class="details-box">
            <ul>
                <li><strong>Product Name:</strong> {{ $product->name }}</li>
                <li><strong>Current Inventory Level:</strong> {{ $product->initial_stock }}</li>
                <li><strong>Alert Threshold Level:</strong> {{ $product->alert_stock_level }}</li>
            </ul>
        </div>
        
        <p>Please review this item and take the necessary actions to initiate a restock order to prevent any supply disruptions.</p>
        
        <p>Best regards,</p>
        <p><strong>Deurali Chemicals Pvt ltd</strong></p>
        
        <div class="footer">
            <p>This is an automated system-generated email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>