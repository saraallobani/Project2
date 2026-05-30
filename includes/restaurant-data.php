<?php
// includes/restaurant-data.php

$meshrider_restaurants = [
    // ==========================================
    // AMMAN RESTAURANTS
    // ==========================================
    1 => [
        'id' => 1,
        'region' => 'amman',
        'name' => 'مطعم فخر الدين',
        'cuisine' => 'عربي / لبناني',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'الدوار الثاني، جبل عمّان',
        'short_desc' => 'من أعرق المطاعم اللبنانية الفاخرة في عمّان.',
        'desc' => 'يقع مطعم فخر الدين في فيلا تاريخية جميلة في جبل عمّان. يقدم أشهى المأكولات اللبنانية والشرقية العريقة في أجواء راقية وكلاسيكية، ويعتبر الوجهة الأولى لكبار الشخصيات وزوار الأردن.',
        'atmosphere' => 'فاخر (Luxury)',
        'amenities' => [
            ['icon' => 'fa-wifi', 'name' => 'واي فاي مجاني'],
            ['icon' => 'fa-parking', 'name' => 'خدمة اصطفاف السيارات'],
            ['icon' => 'fa-chair', 'name' => 'جلسات خارجية'],
            ['icon' => 'fa-wine-glass', 'name' => 'مشروبات فاخرة']
        ],
        'phone' => '+962 6 465 2399',
        'whatsapp' => '962791112222',
        'status' => 'مفتوح للغداء والعشاء',
        'menu' => [
            [
                'title' => 'مازة فخر الدين الباردة',
                'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?auto=format&fit=crop&w=600&q=80',
                'description' => 'تشكيلة من الحمص، المتبل، التبولة، وورق العنب المحضر على الطريقة اللبنانية الأصيلة.',
                'price' => '12 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'مشاوي مشكلة',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                'description' => 'أسياخ من اللحم الضأن الطازج، الشيش طاووق، والكباب مع الخضار المشوية.',
                'price' => '22 دينار',
                'category' => 'الطبق الرئيسي (Main)'
            ],
            [
                'title' => 'حلاوة الجبن',
                'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=600&q=80',
                'description' => 'محشوة بالقشطة الطازجة ومزينة بالفستق الحلبي.',
                'price' => '7 دينار',
                'category' => 'حلويات (Dessert)'
            ]
        ]
    ],
    2 => [
        'id' => 2,
        'region' => 'amman',
        'name' => 'روميرو',
        'cuisine' => 'إيطالي',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'الدوار الثالث، عمّان',
        'short_desc' => 'أقدم وأفخم مطعم إيطالي في العاصمة.',
        'desc' => 'تأسس روميرو في السبعينات كأول مطعم إيطالي أصيل في عمّان. يتميز بجلساته الساحرة وأطباقه التي تحضر بشغف إيطالي حقيقي تحت إشراف طهاة عالميين.',
        'atmosphere' => 'رومانسي / هادئ',
        'amenities' => [
            ['icon' => 'fa-wifi', 'name' => 'واي فاي مجاني'],
            ['icon' => 'fa-tree', 'name' => 'حديقة ساحرة'],
            ['icon' => 'fa-wine-bottle', 'name' => 'قبو نبيذ'],
        ],
        'phone' => '+962 6 464 4227',
        'whatsapp' => '962793334444',
        'status' => 'يُفضل الحجز المسبق',
    ],
    3 => [
        'id' => 3,
        'region' => 'amman',
        'name' => 'هاشم',
        'cuisine' => 'شعبي أردني',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$',
        'location' => 'وسط البلد، عمّان',
        'short_desc' => 'أيقونة عمّان لأشهى الفلافل والحمص منذ عقود.',
        'desc' => 'لا يمكنك زيارة عمّان دون الجلوس في مطعم هاشم بوسط البلد. مطعم شعبي عريق يزوره الجميع من الملوك إلى السياح لتناول ألذ حمص وفلافل في الهواء الطلق.',
        'atmosphere' => 'شعبي / حيوي',
        'amenities' => [
            ['icon' => 'fa-clock', 'name' => 'مفتوح 24 ساعة'],
            ['icon' => 'fa-users', 'name' => 'مناسب للعائلات'],
            ['icon' => 'fa-wallet', 'name' => 'أسعار اقتصادية'],
        ],
        'phone' => '+962 6 463 6440',
        'whatsapp' => '',
        'status' => 'لا حاجة للحجز',
        'menu' => [
            [
                'title' => 'صحن حمص بالزيت',
                'image' => 'https://images.unsplash.com/photo-1577906096429-f73c2c312435?auto=format&fit=crop&w=600&q=80',
                'description' => 'حمص مطحون مع طحينة، ليمون، ومغمور بزيت الزيتون الأردني البكر.',
                'price' => '1.5 دينار',
                'category' => 'الطبق الرئيسي (Main)'
            ],
            [
                'title' => 'فلافل محشي',
                'image' => 'https://images.unsplash.com/photo-1593006526979-4f8fb713ab9b?auto=format&fit=crop&w=600&q=80',
                'description' => 'حبات فلافل مقرمشة محشوة بالبصل والسماق والصنوبر.',
                'price' => '1.0 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'شاي بالنعناع',
                'image' => 'https://images.unsplash.com/photo-1561336313-0bd5e0b27ec8?auto=format&fit=crop&w=600&q=80',
                'description' => 'شاي أسود مع النعناع الطازج والسكر.',
                'price' => '0.5 دينار',
                'category' => 'مشروبات (Drinks)'
            ]
        ]
    ],
    4 => [
        'id' => 4,
        'region' => 'amman',
        'name' => 'مطعم ريم البوادي',
        'cuisine' => 'مأكولات شرقية',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'تلاع العلي، عمّان',
        'short_desc' => 'أجواء عائلية أردنية مع قائمة طعام غنية جداً.',
        'desc' => 'الخيار المفضل للعائلات الكبيرة للاستمتاع بأجواء أردنية أصيلة ومشاوي شهية مع جلسات واسعة ومريحة.',
        'atmosphere' => 'عائلي كبير',
        'amenities' => [
            ['icon' => 'fa-wifi', 'name' => 'واي فاي'],
            ['icon' => 'fa-child', 'name' => 'منطقة لعب للأطفال'],
            ['icon' => 'fa-parking', 'name' => 'مواقف متوفرة'],
        ],
        'phone' => '+962 6 551 5419',
        'whatsapp' => '962795556666',
        'status' => 'متاح',
    ],

    // ==========================================
    // AQABA RESTAURANTS
    // ==========================================
    5 => [
        'id' => 5,
        'region' => 'aqaba',
        'name' => 'روفرز ريترن',
        'cuisine' => 'مأكولات بحرية وعالمية',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'مارينا أيلة، العقبة',
        'short_desc' => 'إطلالة بحرية خلابة مع أطباق عالمية راقية.',
        'desc' => 'يقع في أرقى مناطق العقبة بمارينا أيلة، ويقدم تجربة تناول طعام فاخرة جداً على اليخوت والواجهة البحرية.',
        'atmosphere' => 'رومانسي / فاخر',
        'amenities' => [
            ['icon' => 'fa-anchor', 'name' => 'إطلالة على المارينا'],
            ['icon' => 'fa-music', 'name' => 'موسيقى حية'],
            ['icon' => 'fa-wine-glass', 'name' => 'مشروبات عالمية'],
        ],
        'phone' => '+962 3 209 4000',
        'whatsapp' => '962791234567',
        'status' => 'الحجز مطلوب',
        'menu' => [
            [
                'title' => 'سلطة السلمون المدخن',
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80',
                'description' => 'شرائح السلمون المدخن مع الكيبرز وأوراق الجرجير الطازجة.',
                'price' => '14 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'فيليه الستيك مع جراد البحر',
                'image' => 'https://images.unsplash.com/photo-1544025162-811114b03eb2?auto=format&fit=crop&w=600&q=80',
                'description' => 'طبق سيرف آند تيرف الكلاسيكي، يقدم مع خضار مشوية وصلصة الزبدة والليمون.',
                'price' => '35 دينار',
                'category' => 'الطبق الرئيسي (Main)'
            ]
        ]
    ],
    6 => [
        'id' => 6,
        'region' => 'aqaba',
        'name' => 'علي بابا',
        'cuisine' => 'بحري / أردني',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'وسط المدينة، ساحة الثورة، العقبة',
        'short_desc' => 'أشهر مطاعم الأسماك والبحريات في قلب العقبة.',
        'desc' => 'مطعم علي بابا هو وجهة تقليدية وعريقة في العقبة لاستمتاع بصيادية العقبة الأصلية وأشهى الأسماك الطازجة المصطادة يومياً.',
        'atmosphere' => 'حيوي / عائلي',
        'amenities' => [
            ['icon' => 'fa-fish', 'name' => 'أسماك طازجة'],
            ['icon' => 'fa-chair', 'name' => 'جلسات خارجية'],
            ['icon' => 'fa-wifi', 'name' => 'واي فاي مجاني'],
        ],
        'phone' => '+962 3 201 3901',
        'whatsapp' => '962799998888',
        'status' => 'متاح',
    ],
    7 => [
        'id' => 7,
        'region' => 'aqaba',
        'name' => 'الشامي',
        'cuisine' => 'مشاوي / عربي',
        'stars' => 3,
        'image' => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$',
        'location' => 'المنطقة التجارية، العقبة',
        'short_desc' => 'خيار سريع واقتصادي للمشاوي والمعجنات الساخنة.',
        'desc' => 'مطعم الشامي يقدم أطباق سريعة ولذيذة للزوار بأسعار منافسة جداً، معروف بوجبات الشاورما والمشاوي.',
        'atmosphere' => 'سريع / عملي',
        'amenities' => [
            ['icon' => 'fa-motorcycle', 'name' => 'توصيل مجاني'],
            ['icon' => 'fa-clock', 'name' => 'خدمة سريعة'],
        ],
        'phone' => '+962 3 201 5555',
        'whatsapp' => '',
        'status' => 'متاح',
    ],

    // ==========================================
    // WADI RUM RESTAURANTS
    // ==========================================
    8 => [
        'id' => 8,
        'region' => 'wadirum',
        'name' => 'خيمة الزرب الملكية',
        'cuisine' => 'بدوي أصيل',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1533619043865-1c2e1f42fa25?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'محمية وادي رم الطبيعية',
        'short_desc' => 'تجربة تناول الزرب البدوي المطبوخ تحت الرمال الساخنة.',
        'desc' => 'ليست مجرد وجبة بل تجربة ثقافية كاملة! استمتع بمشاهدة استخراج لحم الخروف المدخن ببطء من تحت رمال الصحراء، وتناوله في خيمة بدوية فاخرة.',
        'atmosphere' => 'ثقافي / صحراوي',
        'amenities' => [
            ['icon' => 'fa-fire', 'name' => 'طبخ تقليدي تحت الأرض'],
            ['icon' => 'fa-music', 'name' => 'عزف ربابة'],
            ['icon' => 'fa-campground', 'name' => 'أجواء بدوية'],
        ],
        'phone' => '+962 79 555 4444',
        'whatsapp' => '962795554444',
        'status' => 'يتطلب حجز مسبق للزرب',
        'menu' => [
            [
                'title' => 'شوربة العدس الصحراوية',
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=600&q=80',
                'description' => 'شوربة عدس دافئة ومتبلة بالكمون، تقدم مع خبز الشراك المحمص.',
                'price' => '4 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'الزرب البدوي الأصيل',
                'image' => 'https://images.unsplash.com/photo-1627308595229-7830f5c9c66e?auto=format&fit=crop&w=600&q=80',
                'description' => 'قطع لحم الخروف المدخنة مع الدجاج والخضار الجذرية، مطبوخة ببطء تحت رمال الصحراء لمدة 4 ساعات.',
                'price' => '25 دينار للشخص',
                'category' => 'الطبق الرئيسي (Main)'
            ],
            [
                'title' => 'شاي بدوي على الحطب',
                'image' => 'https://images.unsplash.com/photo-1561336313-0bd5e0b27ec8?auto=format&fit=crop&w=600&q=80',
                'description' => 'شاي داكن يغلي على جمر الحطب ومطعم بالمريمية الصحراوية.',
                'price' => 'مشمول',
                'category' => 'مشروبات (Drinks)'
            ]
        ]
    ],
    9 => [
        'id' => 9,
        'region' => 'wadirum',
        'name' => 'واحة الصحراء',
        'cuisine' => 'عربي مشكل',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1516684732162-798a0062be99?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'قرية وادي رم',
        'short_desc' => 'بوفيه مفتوح للمأكولات الأردنية في أجواء مكيفة.',
        'desc' => 'خيار مريح بعد يوم طويل من السفاري، يوفر بوفيه مفتوح يرضي كافة الأذواق من مقبلات ساخنة وباردة ومناسف.',
        'atmosphere' => 'مريح / عائلي',
        'amenities' => [
            ['icon' => 'fa-snowflake', 'name' => 'صالة مكيفة'],
            ['icon' => 'fa-buffet', 'name' => 'بوفيه مفتوح'],
            ['icon' => 'fa-wifi', 'name' => 'إنترنت لاسلكي'],
        ],
        'phone' => '+962 79 111 2233',
        'whatsapp' => '962791112233',
        'status' => 'متاح',
    ],

    // ==========================================
    // JERASH RESTAURANTS
    // ==========================================
    10 => [
        'id' => 10,
        'region' => 'jerash',
        'name' => 'البيت اللبناني',
        'cuisine' => 'لبناني',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'شارع الجبل، جرش',
        'short_desc' => 'تناول طعامك وسط أشجار الزيتون وبإطلالة على الآثار.',
        'desc' => 'البيت اللبناني أو "أبو أحمد" هو أشهر مطعم في جرش، وجهة دائمة للملوك والمشاهير، يقدم تجربة طعام لبنانية أردنية غنية مع خدمة لا يعلى عليها.',
        'atmosphere' => 'راقي / طبيعي',
        'amenities' => [
            ['icon' => 'fa-tree', 'name' => 'حديقة جميلة'],
            ['icon' => 'fa-car', 'name' => 'مواقف خاصة'],
            ['icon' => 'fa-wheelchair', 'name' => 'مهيأ للكراسي المتحركة'],
        ],
        'phone' => '+962 2 635 1301',
        'whatsapp' => '962795551111',
        'status' => 'مفتوح',
        'menu' => [
            [
                'title' => 'كبة نية لبنانية',
                'image' => 'kibbeh_nayyeh.png',
                'description' => 'لحم خروف طازج مدقوق مع البرغل والبهارات اللبنانية وزيت الزيتون.',
                'price' => '11 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'مشاوي البيت',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80',
                'description' => 'تشكيلة فاخرة من الكباب الطحلبي، ريش الغنم، وطاووق الدجاج.',
                'price' => '18 دينار',
                'category' => 'الطبق الرئيسي (Main)'
            ]
        ]
    ],
    11 => [
        'id' => 11,
        'region' => 'jerash',
        'name' => 'مطعم استراحة جرش',
        'cuisine' => 'بوفيه مفتوح',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1414235077428-33898dd18d8c?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'داخل المنطقة الأثرية، جرش',
        'short_desc' => 'المطعم الوحيد داخل حرم المدينة الأثرية.',
        'desc' => 'موقع استراتيجي يسمح لك بتناول الغداء وأنت تشاهد أعمدة جرش الرومانية. يقدم بوفيه غني بالمأكولات الشرقية التي تناسب المجموعات السياحية.',
        'atmosphere' => 'تاريخي / سياحي',
        'amenities' => [
            ['icon' => 'fa-landmark', 'name' => 'داخل الآثار'],
            ['icon' => 'fa-users', 'name' => 'مناسب للجروبات'],
            ['icon' => 'fa-camera', 'name' => 'إطلالة تصوير'],
        ],
        'phone' => '+962 2 635 2222',
        'whatsapp' => '',
        'status' => 'مفتوح للغداء فقط',
    ],

    // ==========================================
    // IRBID RESTAURANTS
    // ==========================================
    12 => [
        'id' => 12,
        'region' => 'irbid',
        'name' => 'مطعم قرية النخيل',
        'cuisine' => 'مأكولات أردنية ومشاوي',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1525648199074-cee30ba79a4a?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'طريق الحصن، إربد',
        'short_desc' => 'مجمع مطاعم عائلي يضم مساحات خضراء شاسعة وشلالات مائية.',
        'desc' => 'وجهة العائلات الأولى في إربد، يوفر مساحات واسعة للجلوس في الهواء الطلق مع قائمة طعام تشمل كل أنواع المشاوي والمقبلات العربية.',
        'atmosphere' => 'عائلي / طبيعي',
        'amenities' => [
            ['icon' => 'fa-tree', 'name' => 'حدائق خارجية'],
            ['icon' => 'fa-child', 'name' => 'ملاهي أطفال'],
            ['icon' => 'fa-parking', 'name' => 'مواقف ضخمة'],
        ],
        'phone' => '+962 2 711 5555',
        'whatsapp' => '962797115555',
        'status' => 'متاح',
    ],
    13 => [
        'id' => 13,
        'region' => 'irbid',
        'name' => 'مطاعم الجليسي',
        'cuisine' => 'شاورما ووجبات سريعة',
        'stars' => 3,
        'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$',
        'location' => 'شارع شفيق ارشيدات (شارع الجامعة)، إربد',
        'short_desc' => 'أشهر شاورما في عروس الشمال ومقصد الشباب الدائم.',
        'desc' => 'لا يمكنك زيارة إربد دون تذوق شاورما الجليسي الأيقونية، يقدم وجبات سريعة ولذيذة جداً تناسب ميزانية الطلاب والشباب.',
        'atmosphere' => 'شبابي / سريع',
        'amenities' => [
            ['icon' => 'fa-motorcycle', 'name' => 'توصيل سريع'],
            ['icon' => 'fa-clock', 'name' => 'عمل لساعات متأخرة'],
        ],
        'phone' => '+962 2 727 7777',
        'whatsapp' => '',
        'status' => 'متاح',
    ],

    // ==========================================
    // DEAD SEA RESTAURANTS
    // ==========================================
    14 => [
        'id' => 14,
        'region' => 'deadsea',
        'name' => 'مطعم بانوراما البحر الميت',
        'cuisine' => 'عربي / عالمي',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'مجمع بانوراما البحر الميت',
        'short_desc' => 'إطلالة لا تضاهى على البحر الميت وجبال فلسطين وقت الغروب.',
        'desc' => 'يعتبر مطعم بانوراما الوجهة المثالية لتناول عشاء رومانسي لا يُنسى. يقع على حافة منحدر يوفر رؤية بانورامية كاملة للبحر الميت، ويقدم أشهى الأطباق العربية والعالمية.',
        'atmosphere' => 'رومانسي / بانورامي',
        'amenities' => [
            ['icon' => 'fa-camera', 'name' => 'إطلالة بانورامية للغروب'],
            ['icon' => 'fa-wine-glass', 'name' => 'مشروبات فاخرة'],
            ['icon' => 'fa-parking', 'name' => 'مواقف خاصة'],
            ['icon' => 'fa-wifi', 'name' => 'واي فاي مجاني']
        ],
        'phone' => '+962 5 349 3322',
        'whatsapp' => '962791114455',
        'status' => 'يفضل الحجز المسبق',
        'menu' => [
            [
                'title' => 'سلطة الكينوا والرمان',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=600&q=80',
                'description' => 'سلطة صحية منعشة مع حبوب الرمان والجوز ودبس الرمان.',
                'price' => '12 دينار',
                'category' => 'مقبلات (Starter)'
            ],
            [
                'title' => 'ميكس جريل بانوراما',
                'image' => 'https://images.unsplash.com/photo-1544025162-811114b03eb2?auto=format&fit=crop&w=600&q=80',
                'description' => 'تشكيلة فاخرة من الكباب، الشيش طاووق، وريش الغنم الطازجة.',
                'price' => '28 دينار',
                'category' => 'الطبق الرئيسي (Main)'
            ]
        ]
    ],
    15 => [
        'id' => 15,
        'region' => 'deadsea',
        'name' => 'بوفيه المسلة - ماريوت',
        'cuisine' => 'بوفيه مفتوح',
        'stars' => 5,
        'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$$',
        'location' => 'فندق ماريوت، البحر الميت',
        'short_desc' => 'بوفيه عشاء عالمي يضم محطات طهي حي.',
        'desc' => 'يقدم مطعم المسلة بوفيه غني بالمأكولات الشرقية والغربية مع زوايا متخصصة للمأكولات البحرية، المشاوي، والسلطات، في أجواء عائلية مريحة وراقية.',
        'atmosphere' => 'عائلي / منتجع',
        'amenities' => [
            ['icon' => 'fa-buffet', 'name' => 'بوفيه مفتوح'],
            ['icon' => 'fa-fire', 'name' => 'محطات طهي حي'],
            ['icon' => 'fa-child', 'name' => 'أسعار خاصة للأطفال'],
        ],
        'phone' => '+962 5 356 0400',
        'whatsapp' => '',
        'status' => 'مفتوح يومياً',
        'menu' => [
            [
                'title' => 'بوفيه العشاء المفتوح',
                'image' => 'https://images.unsplash.com/photo-1414235077428-33898dd18d8c?auto=format&fit=crop&w=600&q=80',
                'description' => 'شامل جميع الأطباق الرئيسية، المقبلات، محطات الطهي، والحلويات.',
                'price' => '35 دينار للشخص',
                'category' => 'الطبق الرئيسي (Main)'
            ]
        ]
    ],
    16 => [
        'id' => 16,
        'region' => 'deadsea',
        'name' => 'أكاشيا 레сторан',
        'cuisine' => 'إيطالي / بحري',
        'stars' => 4,
        'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
        'price_level' => '$$',
        'location' => 'شاطئ عمّان السياحي',
        'short_desc' => 'وجبات سريعة ومأكولات إيطالية على الشاطئ.',
        'desc' => 'خيار رائع لمن يقضون يومهم في السباحة والاستجمام، يقدم وجبات البيتزا والباستا الساخنة بالإضافة إلى العصائر الطازجة على بعد أمتار من مياه البحر الميت.',
        'atmosphere' => 'كاجوال / شاطئي',
        'amenities' => [
            ['icon' => 'fa-umbrella-beach', 'name' => 'جلسات شاطئية'],
            ['icon' => 'fa-pizza-slice', 'name' => 'بيتزا إيطالية'],
        ],
        'phone' => '+962 5 356 5555',
        'whatsapp' => '',
        'status' => 'متاح',
    ]
];
