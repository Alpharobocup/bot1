<?php
// تعریف توکن ربات و URL پایه
$bot_token = "7791682125:AAG1o_SDaCdAMnuDF7sDDo9o1wz2icBZB4k";
$base_url = "https://api.telegram.org/bot" . $bot_token;

// دریافت آپدیت از وب‌هوک
$update = file_get_contents("php://input");
$update_array = json_decode($update, true); // تبدیل JSON به آرایه
$bot_user_id = 7385782327;
// فایل برای ذخیره اخطارها
$warningsFile = "warnings.json";
// فایل برای ذخیره پیام‌ها



// بررسی اینکه پیام وجود دارد
if (isset($update_array["message"])) {
    $text = $update_array["message"]["text"] ?? ""; // متن پیام
    $chat_id = $update_array["message"]["chat"]["id"]; // آی‌دی چت
    $user_id = $update_array["message"]["from"]["id"]; // آی‌دی کاربر
    $username = $update_array["message"]["from"]["username"] ?? "ناشناس"; // نام کاربری
    $name = $update_array["message"]["from"]["first_name"] ?? "کاربر"; // نام کاربر
    $message_id = $update_array["message"]["message_id"]; // آی‌دی پیام برای حذف آن
    $chat_type = isset($update_array['message']['chat']['type']) ? $update_array['message']['chat']['type'] : null;

}
if (isset($update_array["callback_query"])) {
    $callback_data = $update_array["callback_query"]["data"];
    $callback_user_id = $update_array["callback_query"]["from"]["id"];
    $callback_username = $update_array["callback_query"]["from"]["first_name"];
    $callback_user_username = $update_array["callback_query"]["from"]["username"];
    
 }




    if ($text == "/commands" || $text == "دستورات" ||   $text == "/commands@Alpha2024_bot") {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "*** لیست دستورات ربات آلفا *** \n
1). دستور   * جستجو متن ( جای متن ، مطلب خواسته شده رو وارد کن ) * 
2). 5 حذف  = حذف 5 پیام آخر \n توجه داشته باشید که عدد وارد شده باید لاتین باشه \n
3).هشت بار متن ___ = جای خط تیره متن مورد نظر رو که میخوای 8 بار تکرار بشه وارد کن \n  مثال : 9 بار متن کتاب ، ربات 9 بار کلمه کتاب رو میفرسته
        "
    ]);
}







// تابع ارسال پاسخ
function send_reply($url, $data) {
    $c = curl_init(); // مقداردهی اولیه curl
    curl_setopt($c, CURLOPT_URL, $url); // تنظیم URL
    curl_setopt($c, CURLOPT_POST, true); // تنظیم متد POST
    curl_setopt($c, CURLOPT_POSTFIELDS, $data); // داده‌های ارسال
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true); // بازگشت نتیجه
    curl_exec($c); // اجرای درخواست
    curl_close($c); // بستن curl
}






?>
