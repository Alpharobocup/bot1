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

$matn_za = json_decode(file_get_contents('matn_na.json'), true);
    $random_matn_za = $matn_za['matn'][array_rand($matn_za['matn'])];
if (preg_match("/\b(من*ط+ق+)\b/u", $text) || preg_match("/\b(م+نطق+|منطقیه+ من|منطقی جونم|من+ظق+|منطقی)\b/u", $text)) {

   if($user_id == 7690271835) {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => " جووونم " .  $random_matn_za
        
    ]);
}
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






// اگر متن "تبریک تولد" دریافت شد
if ($text == "زهرا تولدت مبارک") {
    // ارسال پیام تبریک به همراه دکمه
    send_reply($base_url . "/sendMessage", [
        "chat_id" => $chat_id,
        "text" => "🎉زهرا تولدت مبارک !  ایشالله که تو تمامی مراحل زندگیت موفق باشی  ! 🎵" . "\n بزن رو دکمه پایین آهنگ مخصوص خودته !",
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [
                    [
                        "text" => "🎁 آهنگ ",
                        "callback_data" => "send_birthday_song"
                    ]
                ]
            ]
        ])
    ]);
}

// اگر روی دکمه کلیک شد
if ($callback_data === "send_birthday_song") {
    // فقط کاربر با آی‌دی مشخص اجازه دریافت آهنگ رو داره
    $allowed_user_id = 7690271835;  // اینجا باید آی‌دی کاربر خاص رو وارد کنید

    if ($callback_user_id == $allowed_user_id) {
        // ارسال آهنگ تبریک تولد
        send_reply($base_url ."/sendAudio", [
            "chat_id" => -1002273241190,
            "audio" => new CURLFile("happy_birthday_song.mp3"), // فایل آهنگ در کنار اسکریپت باشد
            "caption" => "🎶 یک آهنگ ویژه برای تبریک تولد تو! 🎂"
        ]);
    } else {
        // ارسال پیام در گروه که کاربر به دکمه پاسخ داده
        send_reply($base_url . "/sendMessage", [
            "chat_id" => $chat_id, // ارسال به گروه
            "text" => "@{$callback_user['username']} تبریک تولد رو دریافت کرد!"
        ]);


        // پاسخ به کلیک دکمه
        send_reply($base_url . "/answerCallbackQuery", [
           'callback_query_id' => $update_array["callback_query"]['id'],
            "text" => "⛔ شما مجاز به دریافت آهنگ نیستید!",
            "show_alert" => true
        ]);
    }
}


// ذخیره وضعیت در فایل (حالت محاوره‌ای)
$conversationFile = "conversations.json";
$messageFile = "messages.json"; // فایل ذخیره پیام‌ها
$conversations = file_exists($conversationFile) ? json_decode(file_get_contents($conversationFile), true) : [];
$messages = file_exists($messageFile) ? json_decode(file_get_contents($messageFile), true) : [];

if (preg_match("/^میخوام بهت بگم @([a-zA-Z0-9_]+)/u", $text, $matches)) {
    $target_username = $matches[1];
    
    $user_avali == $username;
    
    // ذخیره وضعیت کاربر درخواست‌کننده
    $conversations[$user_id] = [
        "status" => "awaiting_message",
        "target_username" => $target_username,
        "chat_id" => $chat_id
    ];
    file_put_contents($conversationFile, json_encode($conversations, JSON_PRETTY_PRINT));
    
    // اطلاع به کاربر که به پی‌وی برود
    send_reply($base_url . "/sendMessage", [
        "chat_id" => $chat_id,
        "text" => "📩 به پی‌وی من بیا تا متنت رو وارد کنی.",
        "reply_to_message_id" => $message_id
    ]);
    
    // پیام در پی‌وی
    send_reply($base_url . "/sendMessage", [
        "chat_id" => $user_id,
        "text" => "✏️ متنی که می‌خوای برای @$target_username بفرستی رو اینجا وارد کن."
    ]);
}

// حالا چک می‌کنیم که پیام از پی‌وی آمده باشد
if (isset($conversations[$user_id]) && $conversations[$user_id]["status"] == "awaiting_message" && $chat_type == 'private') {
    // بررسی که آیا پیام ارسال شده یا نه
    if (!empty($text)) {
        $target_username = $conversations[$user_id]["target_username"];
        $group_chat_id = $conversations[$user_id]["chat_id"];
        
        // ذخیره متن و پایان وضعیت
        $message_to_send = $text;
        unset($conversations[$user_id]);
        file_put_contents($conversationFile, json_encode($conversations, JSON_PRETTY_PRINT));
        
        // ذخیره پیام در فایل
        $messages[$target_username] = $message_to_send;
        file_put_contents($messageFile, json_encode($messages, JSON_PRETTY_PRINT));
        
        // ارسال تاییدیه به کاربر ارسال کننده
        send_reply($base_url . "/sendMessage", [
            "chat_id" => $user_id,
            "text" => "✅ پیام شما برای @$target_username ثبت شد ."
        ]);
        
        // ارسال دکمه به گروه
        send_reply($base_url . "/sendMessage", [
            "chat_id" => $group_chat_id,
            "text" => "📩 یک پیام مخفی برای @$target_username ارسال شد. برای مشاهده روی دکمه زیر کلیک کنید. " . "\n" . "از طرف " . "@" . $username ,
            "reply_markup" => json_encode([
                "inline_keyboard" => [
                    [
                        [
                            "text" => "📨 مشاهده پیام",
                            "callback_data" => "view_message:$user_id:$target_username"
                        ]
                    ]
                ]
            ])
        ]);
    } else {
        // اگر پیام خالی است، هیچ اقدامی انجام نده
        send_reply($base_url . "/sendMessage", [
            "chat_id" => $user_id,
            "text" => "⛔ شما هیچ متنی وارد نکردید. لطفاً یک پیام ارسال کنید."
        ]);
    }
}

if (isset($callback_data) && preg_match("/^view_message:([0-9]+):([a-zA-Z0-9_]+)/", $callback_data, $matches)) {



    $sender_id = $matches[1];
    $target_username = $matches[2];

    // بررسی اینکه آیا کاربر کلیک‌کننده مجاز است
    if ($callback_user_username == $target_username) {   
    
/*     // ارسال پاسخ به callback query تا تایید شود
    send_reply($base_url . "/answerCallbackQuery", [
        'callback_query_id' => $update_array["callback_query"]['id'],
        'text' => "پی وی منو چک کن  ❗  | ☑️ Alpha Team.",
        'show_alert' => true
    ]);*/
    
        // چک کردن اینکه آیا پیام برای کاربر هدف ذخیره شده
        if (isset($messages[$target_username])) {
        
           // ارسال پاسخ به callback query تا تایید شود
 	   send_reply($base_url . "/answerCallbackQuery", [
      	  'callback_query_id' => $update_array["callback_query"]['id'],
   	  'text' => "📨 پیام ارسال‌شده برای شما:\n\n" . $messages[$target_username] . "\n" . "" ,
      	  'show_alert' => true
   	 ]);
        
          /*  // ارسال پیام به کاربر هدف
            send_reply($base_url . "/sendMessage", [
                "chat_id" => $callback_user_id ,
                "text" => "📨 پیام ارسال‌شده برای شما:\n\n" . $messages[$target_username] . "\n" . ""
            ]);*/
            
            // پس از نمایش پیام، آن را از فایل حذف می‌کنیم
            unset($messages[$target_username]);
            file_put_contents($messageFile, json_encode($messages, JSON_PRETTY_PRINT));
        } else {
        	
        	           // ارسال پاسخ به callback query تا تایید شود
 	   send_reply($base_url . "/answerCallbackQuery", [
      	  'callback_query_id' => $update_array["callback_query"]['id'],
   	  'text' => "⛔ پیامی برای شما وجود ندارد. ⛔",
      	  'show_alert' => true
   	 ]);
        
       /*     send_reply($base_url . "/sendMessage", [
                "chat_id" => $callback_user_id ,
                "text" => "⛔ پیامی برای شما وجود ندارد. ⛔"
            ]); */
        }
    } else {
        // عدم مجوز برای مشاهده
      
    send_reply($base_url . "/answerCallbackQuery", [
        'callback_query_id' => $update_array["callback_query"]['id'],
        'text' => " ⛔ این پیام برای شما نیست  ❗  | ☑️ Alpha Team.",
        'show_alert' => true
    ]);
    }
}




















   


/*
// بررسی انواع پیام‌ها برای ری‌اکشن
if (isset($update_array["message"])) {
    $message = $update_array["message"];

if(  $user_id == 7496379220 || $user_id == 5027511248) {
    // اگر پیام از نوع استیکر باشد
    if (isset($message["sticker"])) {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => " نفرست مغز نخودی ! 🚫 ". " @" . $username
        ]);
    }

    // اگر پیام از نوع تاس (Dice) باشد
    elseif (isset($message["dice"])) {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "لطفاً ری‌اکشن نفرست! 🚫 (تاس)". " @" . $username
        ]);
    }

    // اگر پیام شامل ایموجی باشد
    elseif (isset($message["text"]) && preg_match('/[\x{1F600}-\x{1F64F}]/u', $message["text"])) {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "ایموجی  نفرست ونوس اسکل! 🚫 ". " @" . $username
        ]);
    }
}
}*/




 if (stripos($text, "echo ") === 0) {
        // حذف دستور "echo " از ابتدا
        $message_to_echo = substr($text, 5);
        
        // حذف پیام قبلی
        send_reply($base_url . "/deleteMessage", [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ]);

        // ارسال متن جدید
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message_to_echo
        ]);
 }


/*
// بررسی پیام برای کلمه "حقیقت" یا "جرعت"
if (preg_match("/ح+?ق+?ی+?ق+?ت+/u", $text)) {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "شما کلمه 'حقیقت' را ارسال کردید! به زودی سوالی برای شما ارسال خواهد شد. 🎉"
    ]);

    // ارسال سوال تصادفی از فایل جوک‌ها برای حقیقت
    $questions = json_decode(file_get_contents('jokes.json'), true);
    $random_question = $questions['jorat_haghighat'][array_rand($questions['jorat_haghighat'])];
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "سوال شما: $random_question"
    ]);
} */


// فایل برای ذخیره القاب
$nicknamesFile = "nicknames.json";
// بررسی دستور لیست القاب
if ($text == "لقب ها") {
    $nicknames = file_exists($nicknamesFile) ? json_decode(file_get_contents($nicknamesFile), true) : [];

    if (!empty($nicknames)) {
        $list = "📜 لیست القاب کاربران:\n";
        foreach ($nicknames as $user_id => $nickname) {
            // دریافت اطلاعات کاربر برای نام کاربری
            $user_info = json_decode(file_get_contents($base_url . "/getChatMember?chat_id=$chat_id&user_id=$user_id"), true);
            if (isset($user_info['result']['user']['username'])) {
                $username = "@" . $user_info['result']['user']['username'];
                $list .= "- $username: \"$nickname\"\n";
            } else {
                $name = $user_info['result']['user']['first_name'] ?? 'کاربر';
                $user_link = "<a href='tg://user?id=$user_id'>$name</a>";
                $list .= "- $user_link: \"$nickname\"\n";
            }
        }

        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $list,
            'parse_mode' => 'HTML' // فعال کردن لینک‌ها
        ]);
    } else {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "⛔ هیچ لقبی ثبت نشده است."
        ]);
    }
}

if (preg_match("/حذف لقب ها/u", $text)) {
    // پاک کردن تمام داده‌های لقب‌ها
    file_put_contents($nicknamesFile, json_encode([], JSON_PRETTY_PRINT));

    // ارسال پیام تایید
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "تمامی لقب‌ها با موفقیت حذف شدند."
    ]);
}




if (isset($update_array["message"]["reply_to_message"])) {
    $replied_user = $update_array["message"]["reply_to_message"]["from"];
    $replied_user_id = $replied_user["id"];
    $replied_username = $replied_user["username"] ?? null;
    $replied_name = $replied_user["first_name"] ?? "کاربر";

    // لینک به کاربر
    $user_link = $replied_username
        ? "@$replied_username"
        : "<a href='tg://user?id=$replied_user_id'>$replied_name</a>";

    if (preg_match("/لقب\s+(.*)/u", $text, $matches)) {
        $nickname = $matches[1];

        $admins = get_admins($chat_id, $base_url);
        $is_admin = false;
        foreach ($admins as $admin) {
            if ($admin['user']['id'] == $user_id &&   $user_id != 7496379220 && $user_id != 5027511248   ) {
                $is_admin = true;
                break;
            }
        }

        if ($is_admin) {
            $nicknames = file_exists($nicknamesFile) ? json_decode(file_get_contents($nicknamesFile), true) : [];
            $nicknames[$replied_user_id] = $nickname;
            file_put_contents($nicknamesFile, json_encode($nicknames, JSON_PRETTY_PRINT));

            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "✅ لقب \"$nickname\" برای $user_link تنظیم شد.",
                'parse_mode' => 'HTML' // فعال کردن لینک‌ها
            ]);
        } else {
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "⛔ شما ادمین نیستید و نمی‌توانید این دستور را اجرا کنید. " . "@" .  $username 
            ]);
        }
    } elseif ($text == "لقب") {
        $nicknames = file_exists($nicknamesFile) ? json_decode(file_get_contents($nicknamesFile), true) : [];
        if (isset($nicknames[$replied_user_id])) {
            $nickname = $nicknames[$replied_user_id];
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "لقب $user_link: \"$nickname\"",
                'parse_mode' => 'HTML' // فعال کردن لینک‌ها
            ]);
        } else {
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "⛔ $user_link لقبی ندارد.",
                'parse_mode' => 'HTML' // فعال کردن لینک‌ها
            ]);
        }
    }
}
// بررسی پیام‌های ارسالی برای صدا زدن ربات
if (isset($update_array["message"]["text"])) {
    $message_text = $update_array["message"]["text"];
    $sender_id = $update_array["message"]["from"]["id"];
    $sender_name = $update_array["message"]["from"]["first_name"];

    // اگر متن پیام شامل نام کاربری ربات باشد
    if ($message_text == "ربات" || $message_text == "رباتی" || $message_text == "بات") {
        // بارگذاری القاب از فایل
        $nicknames = file_exists($nicknamesFile) ? json_decode(file_get_contents($nicknamesFile), true) : [];

        // بررسی وجود لقب برای فرستنده پیام
        $nickname = isset($nicknames[$sender_id]) ? $nicknames[$sender_id] : null;

        // متن پاسخ
        $response_text = "جانم" . ($nickname ? " $nickname" : "") . "!";

        // ارسال پاسخ
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $response_text
        ]);
    }
}




##
/* بارگذاری پیام‌های قبلی از فایل
    $messages = file_exists($messagesFile) ? json_decode(file_get_contents($messagesFile), true) : [];
    
    // ذخیره پیام جدید
    $message_data = [
        'user_id' => $user_id,
        'username' => $username,
        'name' => $name,
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'timestamp' => time() // زمان ارسال پیام
      
    ];

    // اضافه کردن پیام جدید به آرایه پیام‌ها
    $messages[] = $message_data;

    // ذخیره داده‌ها در فایل JSON
    file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));
    ###
 
   */ 
 
  
    // اگر پیام از طرف ربات است، پاسخ ندهید
    if ($user_id == $bot_user_id) {
        return; // اگر پیام از طرف ربات باشد، هیچ پاسخی ارسال نمی‌شود
    }

    // خوشامدگویی به اعضای جدید
    if (isset($update_array["message"]["new_chat_members"])) {
        foreach ($update_array["message"]["new_chat_members"] as $new_member) {
            $new_user_id = $new_member["id"];
            $new_username = $new_member["username"] ?? "ناشناس";
            $new_name = $new_member["first_name"] ?? "کاربر";
            // ارسال پیام خوشامدگویی به عضو جدید
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "سلام $new_name (@" . $new_username . ") به گروه خوش آمدید! 😊"
            ]);
        }
    }
    
// بررسی برای کلمه "جستجو"
if (preg_match("/\bجستجو\b\s*(.*)/u", $text, $matches)) {
    $search_query = trim($matches[1]);  // استخراج متن بعد از "جستجو"

    if (!empty($search_query)) {
        // ارسال درخواست به گوگل برای جستجو
        $search_url = "https://www.google.com/search?q=" . urlencode($search_query);
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "در حال جستجو برای: $search_query\nنتایج جستجو: $search_url"
        ]);
    } else {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "لطفا یک عبارت برای جستجو وارد کنید."
        ]);
    }
}



    

    // بررسی لینک در پیام
    if (preg_match("/https?:\/\/|www\./i", $text)) {
     
        // چک کردن اینکه آیا پیام در چت خصوصی با ربات است
    if ($chat_id == $user_id || $user_id == 1656900957) {
        // اگر پیام در چت خصوصی است، هیچ کاری نکنیم و اخطار ندهیم
        return;
    }
    
  
        // بارگذاری اطلاعات اخطارها
        $warnings = file_exists($warningsFile) ? json_decode(file_get_contents($warningsFile), true) : [];

        // افزایش تعداد اخطارها
        if (isset($warnings[$chat_id][$user_id])) {
            $warnings[$chat_id][$user_id]++;
        } else {
            $warnings[$chat_id][$user_id] = 1;
        }

        // ذخیره اخطارها در فایل
        file_put_contents($warningsFile, json_encode($warnings));

        // ارسال پیام اخطار یا حذف کاربر
        if ($warnings[$chat_id][$user_id] < 3) {
            $remainingWarnings = 3 - $warnings[$chat_id][$user_id];
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "⚠️ ، ارسال لینک ممنوع است! شما $remainingWarnings اخطار دیگر دارید." . "\n" . "کاربر  " . $username
            ]);
        } else {
            // حذف کاربر از گروه
            send_reply($base_url . "/kickChatMember", [
                'chat_id' => $chat_id,
                'user_id' => $user_id
            ]);

            // ارسال پیام حذف کاربر
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "🚫 $username به دلیل ارسال مکرر لینک از گروه حذف شد."
            ]);

            // ارسال پیام خصوصی به کاربر بعد از حذف شدن با گزینه‌های پشیمانی
           send_reply("https://api.telegram.org/bot$bot_token/sendMessage", [
                'chat_id' => $user_id,
                'text' => "سلام $name ، شما به دلیل ارسال مکرر لینک از گروه حذف شدید.",
            ]);
        }

        // حذف پیام حاوی لینک بعد از ارسال اخطار
        send_reply($base_url . "/deleteMessage", [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ]);
    }

// بررسی دستور /start
if ($text == "/start"  || $text == "/start@Alpha2024_bot" ) {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "\n سلام! به ربات آلفا خوش اومدی 
        چه کمکی از دستم بر میاد ؟ 😊"
    ]);
}
 /*  $text == "/start"
 // بررسی کلمات خاص در پیام و ارسال پاسخ
if (preg_match("/\b(س*لا+م+)\b/u", $text)) {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "سلام! چطورید؟ 😊"
    ]);
}
if (preg_match("/\b(خ+وبی+|چ+ط+و+ری+|حال+ شما|حالت+ چطور|چی‌خب|خ+بی+|احوال‌پرسی)\b/u", $text)) {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' =>  "",
    ]);
}

    preg_match("/\b(در*با+ر+ه+)\b/u", $text) ||

    if ( $text == "/about" || $text == "about") {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "تیم توسعه‌دهنده آلفا در سال 2021 تأسیس شد.\nاین تیم شامل 3 نفر است و در حوزه‌های زیر فعالیت می‌کند:\n- شبیه‌سازی فوتبال (2D و 3D) در RoboCup\n- طراحی وب\n- امنیت نرم‌افزار\n- برنامه‌های آفیس"
        ]);
    }
}

*/
// بررسی دستور "ادمین ها"
if (preg_match("/\bادمین ها\b/u", $text)) {
    $response = file_get_contents("https://api.telegram.org/bot$bot_token/getChatAdministrators?chat_id=$chat_id");
    $admins = json_decode($response, true)['result'];
    
    $members_list = "";
    foreach ($admins as $admin) {
        $members_list .= "ID: " . $admin['user']['id'] . " - نام : " . $admin['user']['first_name'] . "\n";
    }

    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => $members_list ? $members_list : "هیچ ادمینی پیدا نشد."
    ]);
}




// تابع دریافت ادمین‌های گروه
function get_admins($chat_id, $base_url) {
    $url = $base_url . "/getChatAdministrators?chat_id=" . $chat_id;
    $response = json_decode(file_get_contents($url), true);
    if ($response['ok']) {
        return $response['result']; // لیست ادمین‌ها
    } else {
        error_log("خطا در دریافت ادمین‌ها: " . $response['description']);
        return [];
    }
}






       $message_id_to_delete = $update_array["message"]["reply_to_message"]["message_id"];

if (isset($update_array["message"]["text"]) && $text == "حذف") {
    if (isset($update_array["message"]["reply_to_message"])) {

        // ارسال درخواست حذف پیام
        send_reply($base_url . "/deleteMessage", [
            'chat_id' => $chat_id,
            'message_id' => $message_id_to_delete
        ]);
          send_reply($base_url . "/deleteMessage", [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ]);
}
     
}


// بررسی پیام برای دستور 
if (preg_match("/(\d+)\s+حذف/", $text, $matches)) {
if(  $user_id == 7496379220 || $user_id == 5027511248) {
return ; 
}
    $message_count = (int)$matches[1]; // تعداد پیام‌ها برای حذف
    $admins = get_admins($chat_id, $base_url); // دریافت لیست ادمین‌ها

    // بررسی اینکه آیا کاربر ادمین است
    $is_admin = false;
    foreach ($admins as $admin) {
        if ($user_id== 1656900957||  $admin['user']['id'] == $user_id ) {
            $is_admin = true;
            break;
        }
    }

    if ($is_admin) {
        // حذف پیام‌ها
        for ($i = 0; $i < $message_count; $i++) {
            send_reply($base_url . "/deleteMessage", [
                'chat_id' => $chat_id,
                'message_id' => $message_id - $i
            ]);
        }
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "$message_count پیام حذف شد!"
        ]);
    } else {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "شما ادمین نیستید و نمی‌توانید این دستور را اجرا کنید."
        ]);
    }
}


// بررسی دستور ارسال پیام رگباری
if (preg_match("/(\d+)\s+بار\s+متن\s+(.*)/u", $text, $matches)) {
    $repeat_count = (int)$matches[1];  // تعداد پیام‌های ارسال شده
    $message_text = $matches[2]; // متنی که باید ارسال شود

    for ($i = 0; $i < $repeat_count; $i++) {
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message_text
        ]);
    }
}








// بررسی لینک یا یوزرنیم در پیام
if (preg_match("/https?:\/\/|www\./i", $text) || preg_match("/@[\w_]+/i", $text)) {
    $link_or_username = '';

    // چک کردن برای لینک
    if (preg_match("/https?:\/\/|www\./i", $text)) {
        $link_or_username = "لینک: $text";
    }

    // چک کردن برای یوزرنیم
    if (preg_match("/@[\w_]+/i", $text)) {
        $link_or_username = "یوزرنیم: $text";
    }

    // ذخیره لینک یا یوزرنیم در فایل
    $data = file_exists('links_and_usernames.json') ? json_decode(file_get_contents('links_and_usernames.json'), true) : [];
    $data[] = [
        'user_id' => $user_id,
        'username' => $username,
        'name' => $name,
        'chat_id' => $chat_id,
        'text' => $link_or_username,
        'timestamp' => time()
    ];

    file_put_contents('links_and_usernames.json', json_encode($data, JSON_PRETTY_PRINT));
    
    // ارسال پاسخ به کاربر
  /*  send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "لینک یا یوزرنیم شما ثبت شد."
    ]); */
}



// دریافت اطلاعات کاربر
$user_id = $update_array['message']['from']['id'];
$user_name = $update_array['message']['from']['first_name'] ?? 'ناشناس';
$user_username = $update_array['message']['from']['username'] ?? 'ندارد';
$user_phone = $update_array['message']['contact']['phone_number'] ?? 'ندارد';
$chat_id = $update_array['message']['chat']['id'];

// مسیر ذخیره عکس
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/bott/pic/";

// بررسی اینکه آیا پیام شامل عکس است
if (isset($update_array['message']['photo'])) {
    $photo = end($update_array['message']['photo']);  // بزرگترین سایز عکس
    $file_id = $photo['file_id'];
    
    // دریافت لینک دانلود فایل
    $file_url = "https://api.telegram.org/bot$bot_token/getFile?file_id=$file_id";
    $file_info = json_decode(file_get_contents($file_url), true);
    $file_path = $file_info['result']['file_path'];
    $download_url = "https://api.telegram.org/file/bot$bot_token/$file_path";
    
    // نام فایل برای ذخیره‌سازی
    $file_name = $upload_dir . $file_id . ".jpg";
    
// تنظیم منطقه زمانی به تهران
date_default_timezone_set('Asia/Tehran');
// دریافت زمان فعلی به فرمت مشخص
$current_time = date('Y-m-d H:i:s');
// ذخیره عکس و ارسال پیام تایید آپلود
if (file_put_contents($file_name, $file_info)) {
    // ارسال پیام تایید آپلود به همراه زمان
    send_reply($base_url . "/sendMessage", [
        'chat_id' => 7388352162,
        'text' => "عکس شما با موفقیت آپلود شد: $file_name\nزمان آپلود: $current_time"
    ]);

    } else {
        // ارسال پیام خطا در آپلود
        send_reply($base_url . "/sendMessage", [
            'chat_id' => 7388352162,
            'text' => "متاسفانه مشکلی در آپلود عکس به وجود آمد."
        ]);
    }
    
    
    
    // دانلود فایل
    file_put_contents($file_name, file_get_contents($download_url));

    // ذخیره اطلاعات فرستنده و عکس
    $photo_data = [
        'user_id' => $user_id,
        'user_name' => $user_name,
        'user_username' => $user_username,
        'user_phone' => $user_phone,
        'chat_id' => $chat_id,
        'photo_file' => $file_name,
        'timestamp' => time(),
    ];

    // ذخیره در فایل JSON
    $photos = file_exists('photos.json') ? json_decode(file_get_contents('photos.json'), true) : [];
    $photos[] = $photo_data;
    file_put_contents('photos.json', json_encode($photos, JSON_PRETTY_PRINT));
}



// اطلاعات ربات تلگرام

$groupLink = 'https://t.me/dirrrudu'; // لینک دعوت به گروه (به عنوان مثال: https://t.me/joinchat/XXXXXXX)
$phoneNumbersFile = 'numbers.txt'; // فایل حاوی شماره‌های ناشناس

// خواندن شماره‌ها از فایل
$phoneNumbers = file($phoneNumbersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// تابع برای ارسال درخواست به تلگرام
function sendRequest($url, $data = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// ارسال دعوت به گروه
foreach ($phoneNumbers as $phoneNumber) {
    // شماره تلفن را به URL تبدیل می‌کنیم و درخواست می‌دهیم
    $url = $base_url  . "/inviteToGroupLink";
    $data = [
        'chat_id' => $groupLink, // لینک گروه شما
        'phone_number' => $phoneNumber // شماره‌ای که می‌خواهید دعوت کنید
    ];
    
    // ارسال درخواست برای اضافه کردن عضو به گروه
    $result = sendRequest($url, $data);

    if ($result['ok']) {
        echo "Added $phoneNumber to the group successfully.\n";
    } else {
        echo "Failed to add $phoneNumber: " . $result['description'] . "\n";
    }
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





if ($text == "/jorat" || $text == "جرأت حقیقت" || $text == "/jorat@Alpha2024_bot") {
    send_reply($base_url . "/sendMessage", [
        'chat_id' => $chat_id,
        'text' => "🎲 بازی جرأت و حقیقت 🎲\n\n"
            . "🔸 در این بازی شما می‌توانید بین دو گزینه انتخاب کنید:\n"
            . "1️⃣ جرأت: یک چالش انجام دهید.\n"
            . "2️⃣ حقیقت: به یک سؤال شخصی پاسخ دهید.\n\n"
            . "✅ ابتدا با فشردن دکمه *اعلام آمادگی* وارد بازی شوید.\n"
            . "✅ پس از اینکه همه آماده شدند، دکمه *شروع بازی* را بزنید.\n"
            . "✅ نوبت هر بازیکن که باشد، پس از پاسخ، دکمه *من جواب دادم* را بزند تا نوبت بعدی شروع شود.\n\n"
            . "📢 بیایید شروع کنیم! 👇",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "پایه ام 📢", 'callback_data' => 'ready']],
                [['text' => "شروع بازی", 'callback_data' => 'start_game']],
                [['text' => " جواب دادم ☑️ ", 'callback_data' => 'answered']],
                [['text' => "پایان بازی", 'callback_data' => 'end_game']]
            ]
        ])
    ]);

}

// تعریف متغیرها
$ready_players = file_exists('ready_players.json') ? json_decode(file_get_contents('ready_players.json'), true) : [];
$current_player_index = file_exists('current_player_index.json') ? json_decode(file_get_contents('current_player_index.json'), true) : -1;
$jokes_file = 'jokes.json'; // فایل سوالات جرأت حقیقت
$current_question_user = null; // ذخیره کاربر فعلی که سوال از او پرسیده می‌شود

// دریافت و پردازش callback query‌ها
if (isset($update_array["callback_query"])) {
    $callback_data = $update_array["callback_query"]["data"];
    $callback_user_id = $update_array["callback_query"]["from"]["id"];
    $callback_username = $update_array["callback_query"]["from"]["first_name"];
    $callback_user_username = $update_array["callback_query"]["from"]["username"];
    $chat_id = $update_array["callback_query"]["message"]["chat"]["id"];

    // مدیریت callback data
    if ($callback_data == "ready") {
        // اگر کاربر اعلام آمادگی کرده
        if (!in_array($callback_user_id, array_column($ready_players, 'id'))) {
            $ready_players[] = ['id' => $callback_user_id, 'name' => $callback_username, 'username' => $callback_user_username];
            file_put_contents('ready_players.json', json_encode($ready_players, JSON_PRETTY_PRINT));
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => $callback_username  ."  اعلام آمادگی کرد! 👏"
            ]);
        } else {
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "شما قبلاً اعلام آمادگی کرده‌اید!"
            ]);
        }
    } elseif ($callback_data == "start_game") {
        // اگر بازی شروع شود
        if (count($ready_players) < 2) {
            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "تعداد بازیکنان کافی نیست! حداقل 2 نفر باید اعلام آمادگی کنند."
            ]);
        } else {
            $current_player_index = 0;
            file_put_contents('current_player_index.json', json_encode($current_player_index));

            // ارسال تعداد و اسامی بازیکنان
            $players_list = "بازیکنان آماده:\n";
            foreach ($ready_players as $player) {
                if ($player['username']) {
                    // اگر یوزرنیم وجود داشته باشد، از آن استفاده می‌کنیم
                    $players_list .= "@" . $player['username'] . "\n";
                } else {
                    // اگر یوزرنیم نداشته باشد، اسم را به پروفایل لینک می‌کنیم
                    $players_list .= "<a href='tg://user?id=" . $player['id'] . "'>" . $player['name'] . "</a>\n";
                }
            }

            send_reply($base_url . "/sendMessage", [
                'chat_id' => $chat_id,
                'text' => "بازی شروع شد! 🎉\n\n" . $players_list,
                'parse_mode' => 'HTML'
]);

            $current_player = $ready_players[$current_player_index];

            // بارگذاری سوالات از فایل jokes.json
            if (file_exists($jokes_file)) {
                $jokes_data = json_decode(file_get_contents($jokes_file), true);
                if (isset($jokes_data['jorat_haghighat']) && is_array($jokes_data['jorat_haghighat'])) {
                    // انتخاب یک سوال رندوم از آرایه
                    $random_question = $jokes_data['jorat_haghighat'][array_rand($jokes_data['jorat_haghighat'])];

                    // ارسال سوال به نفر اول
                    send_reply($base_url . "/sendMessage", [
                        'chat_id' => $chat_id,
                        'text' => "نوبت به " . ($current_player['username'] ? "@" . $current_player['username'] : "<a href='tg://user?id=" . $current_player['id'] . "'>" . $current_player['name'] . "</a>") . " رسیده است. سوال جرأت حقیقت: \n" . $random_question,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode([ 
                            'inline_keyboard' => [
                                [['text' => ' 👍 من جواب دادم', 'callback_data' => 'answered']]
                            ]
                        ])
                    ]);
                } else {
                    send_reply($base_url . "/sendMessage", [
                        'chat_id' => $chat_id,
                        'text' => "متاسفانه سوالات جرأت حقیقت پیدا نشد."
                    ]);
                }
            } else {
                send_reply($base_url . "/sendMessage", [
                    'chat_id' => $chat_id,
                    'text' => "فایل سوالات جرأت حقیقت موجود نیست."
                ]);
            }
        }
    } elseif ($callback_data == "answered") {
    // بررسی نوبت بازیکن
    if ($callback_user_id != $ready_players[$current_player_index]['id'] && $callback_user_id != 1656900957 && $callback_user_id != 2108885706 ) {
        // اگر کاربر نوبت خودش نباشد
        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "❌ شما نمی‌توانید این کار را انجام دهید! نوبت شما نیست." . "@" . $callback_user_username 
        ]);
    } else {
        // اگر کاربر نوبت خودش باشد
        $current_player_index++;
        if ($current_player_index >= count($ready_players)) {
            $current_player_index = 0; // شروع مجدد از بازیکن اول
        }
        file_put_contents('current_player_index.json', json_encode($current_player_index));
        $current_player = $ready_players[$current_player_index];

        // بارگذاری سوالات از فایل jokes.json
        if (file_exists($jokes_file)) {
            $jokes_data = json_decode(file_get_contents($jokes_file), true);
            if (isset($jokes_data['jorat_haghighat']) && is_array($jokes_data['jorat_haghighat'])) {
                // انتخاب یک سوال رندوم از آرایه
                $random_question = $jokes_data['jorat_haghighat'][array_rand($jokes_data['jorat_haghighat'])];

                // ارسال سوال به نفر بعدی
                send_reply($base_url . "/sendMessage", [
                    'chat_id' => $chat_id,
                    'text' => "🔴 نوبت به " . ($current_player['username'] ? "@" . $current_player['username'] : "<a href='tg://user?id=" . $current_player['id'] . "'>" . $current_player['name'] . "</a>") . " رسیده است. سوال جرأت حقیقت: \n" . $random_question,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([ 
                        'inline_keyboard' => [
                            [['text' => '👍 من جواب دادم', 'callback_data' => 'answered']]
                        ]
                    ])
                ]);
            }
        }
    }
}
elseif ($callback_data == "end_game") {
        // اگر بازی پایان یابد
        // پاک کردن فایل‌های ذخیره‌شده برای شروع بازی جدید
        unlink('ready_players.json');
        unlink('current_player_index.json');

        send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' => "بازی پایان یافت! اطلاعات ذخیره‌شده پاک شد و آماده بازی بعدی هستیم."
        ]);
    }

    // ارسال پاسخ به callback query تا تایید شود
    send_reply($base_url . "/answerCallbackQuery", [
        'callback_query_id' => $update_array["callback_query"]['id'],
        'text' => "پاسخ ثبت شد ❗  | ☑️ Alpha Team.",
        'show_alert' => false
    ]);
}







// شماره پایه
$baseNumber = "09123456512";
$baseNumber1 = "09361232435";
$baseNumber2 = "09224513698";

// تابع برای تولید شماره‌های تلفن با تغییر 5 رقم آخر
function generatePermutedNumbers($baseNumber, $totalNumbers = 100) {
    $numbers = [];
    
    // اطمینان از اینکه فقط 100 شماره تولید می‌شود
    while (count($numbers) < $totalNumbers) {
        // گرفتن 5 رقم آخر شماره
        $lastFiveDigits = substr($baseNumber, -5);
        
        // تبدیل 5 رقم آخر به آرایه
        $digits = str_split($lastFiveDigits);
        
        // جابجایی ارقام 5 رقمی به صورت تصادفی
        shuffle($digits);
        
        // ایجاد شماره جدید
        $newNumber = substr($baseNumber, 0, -5) . implode("", $digits);
        
        // افزودن به آرایه اگر تکراری نبود
        if (!in_array($newNumber, $numbers)) {
            $numbers[] = $newNumber;
        }
    }
    
    return $numbers;
}

// تولید شماره‌ها
$phoneNumbers = generatePermutedNumbers($baseNumber, 30);
if($text == "همراه" && $user_id == 1656900957){
// نمایش شماره‌ها
foreach ($phoneNumbers as $phone) {
     send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' =>  $phone . "\n"
        ]);
}
}


function generatePermutedNumbers1($baseNumber1, $totalNumbers = 100) {
    $numbers = [];
    
    // اطمینان از اینکه فقط 100 شماره تولید می‌شود
    while (count($numbers) < $totalNumbers) {
        // گرفتن 5 رقم آخر شماره
        $lastFiveDigits = substr($baseNumber1, -5);
        
        // تبدیل 5 رقم آخر به آرایه
        $digits = str_split($lastFiveDigits);
        
        // جابجایی ارقام 5 رقمی به صورت تصادفی
        shuffle($digits);
        
        // ایجاد شماره جدید
        $newNumber = substr($baseNumber1, 0, -5) . implode("", $digits);
        
        // افزودن به آرایه اگر تکراری نبود
        if (!in_array($newNumber, $numbers)) {
            $numbers[] = $newNumber;
        }
    }
    
    return $numbers;
}

// تولید شماره‌ها
$phoneNumbers = generatePermutedNumbers1($baseNumber1, 30);
if($text == "ایرانسل"  && $user_id== 1656900957){
// نمایش شماره‌ها
foreach ($phoneNumbers as $phone) {
     send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' =>  $phone . "\n"
        ]);
}
}

function generatePermutedNumbers2($baseNumber2, $totalNumbers = 100) {
    $numbers = [];
    
    // اطمینان از اینکه فقط 100 شماره تولید می‌شود
    while (count($numbers) < $totalNumbers) {
        // گرفتن 5 رقم آخر شماره
        $lastFiveDigits = substr($baseNumber2, -5);
        
        // تبدیل 5 رقم آخر به آرایه
        $digits = str_split($lastFiveDigits);
        
        // جابجایی ارقام 5 رقمی به صورت تصادفی
        shuffle($digits);
        
        // ایجاد شماره جدید
        $newNumber = substr($baseNumber2, 0, -5) . implode("", $digits);
        
        // افزودن به آرایه اگر تکراری نبود
        if (!in_array($newNumber, $numbers)) {
            $numbers[] = $newNumber;
        }
    }
    
    return $numbers;
}

// تولید شماره‌ها
$phoneNumbers = generatePermutedNumbers2($baseNumber2,40);
if($text == "رایتل" && $user_id== 1656900957 ){
// نمایش شماره‌ها
foreach ($phoneNumbers as $phone) {
     send_reply($base_url . "/sendMessage", [
            'chat_id' => $chat_id,
            'text' =>  $phone . "\n"
        ]);
}
}



?>