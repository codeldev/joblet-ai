<?php

declare(strict_types=1);

//use App\Models\Order;
//use App\Models\Payment;
//use App\Services\Models\UserService;
//use Illuminate\Support\Facades\Route;

//Route::get('mailable', static function ()
//{
//    $data = [
//        'name'    => 'Jane Smith',
//        'email'   => 'jane.smith@example.com',
//        'message' => "I wanted to provide feedback about your new website design. The navigation is much more intuitive now, and I love the dark mode option!\n\nHowever, I noticed that the contact form sometimes takes a while to submit on mobile devices. Overall, great improvements!",
//    ];
//
//    return (new App\Notifications\Auth\LoginLinkNotification)->toMail(UserService::getSupportUser());
//    //    return new App\Notifications\Contact\FeedbackMessageNotification($data)
//    //        ->toMail(UserService::getSupportUser());
//});

//Route::get('pdf', static function ()
//{
//    $order   = Order::find('0196ba6d-0c92-73ce-a684-ee818f65c74b');
//    // Payment::factory()->create(['order_id' => $order->id, 'user_id' => $order->user_id]);
//    $pdfData = new App\Services\Invoice\InvoiceDataService(order: $order)->build();
//    $service = new App\Services\Invoice\PdfService;
//    $service
//        ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
//        ->setPageSize(size: App\Enums\PageSizeEnum::A4)
//        ->setPageView(view: 'pdf.invoice')
//        ->setPdfData(data: $pdfData)
//        ->createTempFileName()
//        ->generate();
//
//    return $service->outputToBrowser();
//});
