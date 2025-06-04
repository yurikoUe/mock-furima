<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $buyerName;
    public $productName;
    public $orderId;
    public $sellerName;

    public function __construct($buyerName, $productName, $orderId, $sellerName)
    {
        $this->buyerName = $buyerName;
        $this->productName = $productName;
        $this->orderId = $orderId;
        $this->sellerName = $sellerName;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->markdown('emails.orders.completed');
    }
}
