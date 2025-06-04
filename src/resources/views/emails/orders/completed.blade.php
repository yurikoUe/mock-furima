@component('mail::message')
# 取引完了のお知らせ

{{ $buyerName }}さんが取引が完了させ、　{{ $sellerName }}さんを評価しました。

商品名: **{{ $productName }}**

商品取引ページを開いて、{{ $buyerName }}を評価してください。

ご利用ありがとうございました！

@component('mail::button', ['url' => route('chat.show', $orderId)])
相手を評価する
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
