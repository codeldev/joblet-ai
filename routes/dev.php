<?php

declare(strict_types=1);

// use App\Models\Order;
// use App\Models\Payment;
// use App\Services\Models\UserService;
// use Illuminate\Support\Facades\Route;
//
// Route::get('mailable', static function ()
// {
//    $data = [
//        'name' => 'Jane Smith',
//        'email' => 'jane.smith@example.com',
//        'message' => "I wanted to provide feedback about your new website design. The navigation is much more intuitive now, and I love the dark mode option!\n\nHowever, I noticed that the contact form sometimes takes a while to submit on mobile devices. Overall, great improvements!",
//    ];
//    return (new App\Notifications\Auth\LoginLinkNotification)->toMail(UserService::getSupportUser());
//    return new App\Notifications\Contact\FeedbackMessageNotification($data)
//        ->toMail(UserService::getSupportUser());
// });
//
// Route::get('pdf', static function ()
// {
//     $order = Order::find('0196ab40-fd74-72a7-a3eb-5a892dd0ede8');
//
//     $order = UserService::getSupportUser()->orders()->latest()->first();
//     Payment::factory()->create([
//         'order_id'       => $order->id,
//         'user_id'        => $order->user_id,
//         'amount'         => $order->price,
//     ]);
//        $order   = Order::factory()->for(UserService::getSupportUser())->create();
//    $pdfData = new App\Services\Invoice\InvoiceDataService(order: $order)->build();
//
//    ray($order, $pdfData);
//
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
// // });
