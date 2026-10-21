-- =====================================================================
--  RWANDA EXPLORER - Seed data (import AFTER schema.sql)
--  All images are local files under assets/img/  (no external hotlinks)
--
--  One admin account is seeded:
--    admin@rwandaexplorer.rw / Admin@2026  (change it after first login)
--  Any NEW admin registration at /admin/register stays 'pending' until
--  the existing admin approves it in Admin -> Admin requests.
--
--  Demo customer logins:
--    traveler@rwandaexplorer.rw / traveler123
--    hotel@rwandaexplorer.rw    / hotel123
--    operator@rwandaexplorer.rw / operator123
--    guide@rwandaexplorer.rw    / guide123
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- users  (customers + the single seeded admin; password_hash = bcrypt,
-- works with password_verify)
-- ---------------------------------------------------------------------
INSERT INTO `users` (`id`,`name`,`email`,`phone`,`password_hash`,`role`,`nationality`,`loyalty_points`,`status`,`email_verified`) VALUES
 (1,'Alice Mukamana','alice.mukamana@example.com','+250788000001','$2y$10$qEBKqWPGcy0glWLC89Fm9.k.cldOgemb4QyfmgOJtdwD/Fgzopu8W','customer','Rwanda',240,'active',1),
 (2,'Diane Uwase','diane.uwase@example.com','+250788000002','$2y$10$hgJyZGT5ARQ6a0hNAuNiwuscMUp3hgOwFv9u0NrrUMyjtl/9i47ue','customer','Rwanda',0,'active',1),
 (3,'Virunga Lodge Mgr','hotel@rwandaexplorer.rw','+250788000003','$2y$10$vYzKjlvC48YntAEQoWtBlu1S1bBw/MUssVpGQtbM2CHIZSnGpaVUG','customer','Rwanda',0,'active',1),
 (4,'Emmanuel Tours','operator@rwandaexplorer.rw','+250788000004','$2y$10$7F6lIso7eMR49b3JpQAiBuke2nyn8FWiLwEB9O/jwTc8gJbVUf/TC','customer','Rwanda',0,'active',1),
 (5,'Emmanuel Niyonsaba','guide@rwandaexplorer.rw','+250788000005','$2y$10$56dwrIsHAD03hoxsUMN3he71OJBjJfUxSwsnujwl28ktUM8xz4K7a','customer','Rwanda',0,'active',1),
 (6,'Jean Uwase','traveler@rwandaexplorer.rw','+250788000006','$2y$10$Kk/NvjwUS7n1BbR2LcdhZutvKx7j5dLIQxuJMVgVSAeSKsK9.6/jC','customer','Rwanda',2450,'active',1),
 (7,'Admin','admin@rwandaexplorer.rw',NULL,'$2y$10$VYCTmkTSoGgJMdNYPPmzzOuoiw2C2YvPYAPG4dT8OMuXh8Ve3m9Bq','admin',NULL,0,'active',1);

-- ---------------------------------------------------------------------
-- destinations (5 featured)
-- ---------------------------------------------------------------------
INSERT INTO `destinations`
 (`id`,`slug`,`name`,`region`,`short_description`,`description`,`hero_image`,`gallery`,`activities`,`best_season`,`entry_fee`,`lat`,`lng`,`how_to_get_there`,`is_featured`) VALUES
 (1,'volcanoes-national-park','Volcanoes National Park','Northern Province',
  'Home to the endangered mountain gorillas.',
  'Volcanoes National Park shelters five of the eight Virunga volcanoes and the world''s most accessible population of mountain gorillas. Trek through bamboo forest and Hagenia woodland to spend a magical hour with a habituated gorilla family.',
  'assets/img/hero-volcanoes.jpg',
  JSON_ARRAY('assets/img/hero-volcanoes.jpg','assets/img/dest-gorilla.jpg','assets/img/dest-tea.jpg'),
  JSON_ARRAY('Gorilla Trekking','Volcano Hiking','Golden Monkey Trek'),
  'Jun - Sep', 1500.00, -1.4844000, 29.5918000,
  '3 hours'' drive from Kigali to Kinigi, Musanze. Private transfer or shared shuttle available.', 1),
 (2,'lake-kivu','Lake Kivu','Western Province',
  'Beaches, islands and serene sunsets.',
  'One of Africa''s Great Lakes, Lake Kivu offers golden beaches, island hopping, kayaking and some of the most spectacular sunsets on the continent. The lakeside towns of Rubavu and Karongi are perfect bases.',
  'assets/img/hero-kivu.jpg',
  JSON_ARRAY('assets/img/hero-kivu.jpg','assets/img/gallery-lake.jpg','assets/img/gallery-boat.jpg'),
  JSON_ARRAY('Beach','Boat Trips','Kayaking','Island Hopping'),
  'Year-round', 0.00, -1.6792000, 29.2608000,
  '2.5 hours'' drive west of Kigali to Rubavu (Gisenyi).', 1),
 (3,'nyungwe-forest','Nyungwe Forest National Park','Southern Province',
  'Ancient rainforest and a famous canopy walk.',
  'Nyungwe is one of Africa''s oldest rainforests, home to 13 primate species and over 300 bird species. The suspended canopy walkway 60 metres above the forest floor is a bucket-list highlight.',
  'assets/img/hero-nyungwe.jpg',
  JSON_ARRAY('assets/img/hero-nyungwe.jpg','assets/img/dest-canopy.jpg'),
  JSON_ARRAY('Canopy Walk','Chimpanzee Trek','Birdwatching'),
  'Jun - Aug', 60.00, -2.4667000, 29.2000000,
  '5 hours'' drive south-west of Kigali, or 30 minutes from Kamembe airport.', 1),
 (4,'akagera-national-park','Akagera National Park','Eastern Province',
  'Big Five safari on the savannah.',
  'Rwanda''s only savannah national park, Akagera is now a Big Five destination with lions, elephants, rhinos, buffalo and leopards. Boat cruises on Lake Ihema reveal hippos and crocodiles.',
  'assets/img/hero-akagera.jpg',
  JSON_ARRAY('assets/img/hero-akagera.jpg','assets/img/dest-safari.jpg'),
  JSON_ARRAY('Safari','Boat Cruise','Birdwatching'),
  'Jun - Sep', 50.00, -1.7000000, 30.7667000,
  '2.5 hours'' drive east of Kigali.', 1),
 (5,'kigali','Kigali','Kigali City',
  'Africa''s cleanest capital - culture and history.',
  'Rwanda''s vibrant capital blends poignant history at the Kigali Genocide Memorial with bustling markets, world-class coffee and a rising art scene. It''s the perfect start or end to any Rwandan journey.',
  'assets/img/hero-kigali.jpg',
  JSON_ARRAY('assets/img/hero-kigali.jpg','assets/img/dest-memorial.jpg','assets/img/gallery-city.jpg'),
  JSON_ARRAY('City Tour','Culture','Genocide Memorial','Markets'),
  'Year-round', 0.00, -1.9441000, 30.0623000,
  'Kigali International Airport (KGL) is 12 km from the city centre.', 1);

-- ---------------------------------------------------------------------
-- hotels (10)
-- ---------------------------------------------------------------------
INSERT INTO `hotels`
 (`id`,`owner_id`,`destination_id`,`slug`,`name`,`type`,`stars`,`city`,`description`,`amenities`,`base_price`,`currency`,`cover_image`,`lat`,`lng`,`phone`,`email`,`rating`,`review_count`,`status`,`is_featured`) VALUES
 (1,3,1,'virunga-lodge','Virunga Lodge','lodge',5,'Musanze',
  'Award-winning luxury lodge perched on a ridge with panoramic views of the Virunga volcanoes and twin lakes.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Parking','Spa'), 420.00,'USD',
  'assets/img/hotel-1.jpg', -1.4844000,29.5918000,'+250788100001','stay@virungalodge.rw',4.90,312,'approved',1),
 (2,NULL,2,'kivu-serena-hotel','Kivu Serena Hotel','hotel',5,'Rubavu',
  'Beachfront five-star hotel on the shores of Lake Kivu with a private beach and outdoor pool.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Parking','Gym'), 310.00,'USD',
  'assets/img/hotel-2.jpg', -1.6792000,29.2608000,'+250788100002','res@kivuserena.rw',4.80,428,'approved',1),
 (3,NULL,3,'nyungwe-forest-lodge','Nyungwe Forest Lodge','lodge',5,'Nyungwe',
  'Tea-estate luxury lodge at the edge of Nyungwe Forest, with infinity pool overlooking the canopy.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Spa'), 540.00,'USD',
  'assets/img/hotel-3.jpg', -2.4667000,29.2000000,'+250788100003','stay@nyungwelodge.rw',4.90,186,'approved',1),
 (4,NULL,4,'akagera-game-lodge','Akagera Game Lodge','lodge',4,'Kayonza',
  'Comfortable lodge inside Akagera National Park with panoramic views over Lake Ihema.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Parking'), 195.00,'USD',
  'assets/img/hotel-4.jpg', -1.7000000,30.7667000,'+250788100004','info@akageragamelodge.rw',4.60,154,'approved',1),
 (5,NULL,5,'kigali-marriott-hotel','Kigali Marriott Hotel','hotel',5,'Kigali',
  'Modern city-centre five-star hotel with rooftop pool, spa and multiple restaurants.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Gym','Parking'), 285.00,'USD',
  'assets/img/hotel-5.jpg', -1.9441000,30.0623000,'+250788100005','res@marriottkigali.rw',4.80,512,'approved',1),
 (6,NULL,5,'radisson-blu-kigali','Radisson Blu Hotel & Convention Centre','hotel',4,'Kigali',
  'Contemporary business hotel with the largest convention centre in Rwanda.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Gym','Parking'), 175.00,'USD',
  'assets/img/hotel-6.jpg', -1.9686000,30.0586000,'+250788100006','res@radissonkigali.rw',4.60,394,'approved',1),
 (7,NULL,2,'lake-kivu-royal-resort','Lake Kivu Royal Resort','resort',4,'Rubavu',
  'Relaxed lakeside resort with private beach, watersports and family-friendly bungalows.',
  JSON_ARRAY('Wi-Fi','Breakfast','Pool','Beach'), 165.00,'USD',
  'assets/img/hotel-7.jpg', -1.6792000,29.2608000,'+250788100007','hello@kivuroyal.rw',4.50,121,'approved',0),
 (8,NULL,1,'inzu-mountain-lodge','Inzu Mountain Lodge','lodge',4,'Musanze',
  'Warm, family-run lodge with volcano views, a short drive from the gorilla trekking trailhead.',
  JSON_ARRAY('Wi-Fi','Breakfast','Parking'), 210.00,'USD',
  'assets/img/hotel-8.jpg', -1.5000000,29.6000000,'+250788100008','stay@inzulodge.rw',4.70,97,'approved',0),
 (9,NULL,5,'kigali-garden-guesthouse','Kigali Garden Guesthouse','guesthouse',3,'Kigali',
  'Friendly budget guesthouse with a lush garden, home-cooked breakfast and free Wi-Fi.',
  JSON_ARRAY('Wi-Fi','Breakfast','Parking'), 68.00,'USD',
  'assets/img/hotel-9.jpg', -1.9500000,30.0600000,'+250788100009','book@kigaligarden.rw',4.40,88,'approved',0),
 (10,NULL,2,'nyamasheke-homestay','Nyamasheke Community Homestay','homestay',3,'Nyamasheke',
  'Authentic community homestay on the coffee belt above Lake Kivu - income supports local families.',
  JSON_ARRAY('Wi-Fi','Breakfast'), 42.00,'USD',
  'assets/img/hotel-10.jpg', -2.3500000,29.1500000,'+250788100010','stay@nyamashekehomestay.rw',4.60,53,'approved',0);

-- ---------------------------------------------------------------------
-- rooms
-- ---------------------------------------------------------------------
INSERT INTO `rooms` (`hotel_id`,`name`,`room_type`,`description`,`size_sqm`,`beds`,`capacity`,`price`,`total_units`,`features`) VALUES
 (1,'Standard Room','standard','Garden-view room with en-suite bathroom.',28,'1 Queen',2,420.00,6,JSON_ARRAY('Garden view','En-suite','Wi-Fi')),
 (1,'Deluxe Volcano Room','deluxe','Balcony with direct volcano views.',38,'1 King',2,520.00,4,JSON_ARRAY('Balcony','Minibar','Rain shower')),
 (1,'Family Suite','family','Two-room suite ideal for families.',55,'1 King + 2 Single',4,680.00,2,JSON_ARRAY('Living area','2 bathrooms')),
 (2,'Standard Lake Room','standard','Comfortable room with partial lake view.',30,'1 Queen',2,310.00,20,JSON_ARRAY('Lake view','En-suite','Wi-Fi')),
 (2,'Beach Deluxe','deluxe','Direct beach access and terrace.',40,'1 King',2,410.00,10,JSON_ARRAY('Beach access','Terrace','Minibar')),
 (5,'Executive Room','deluxe','City-view executive room with lounge access.',42,'1 King',2,285.00,30,JSON_ARRAY('City view','Lounge access','Wi-Fi')),
 (6,'Superior Room','standard','Bright contemporary room.',32,'1 Queen',2,175.00,40,JSON_ARRAY('Wi-Fi','Work desk')),
 (9,'Garden Room','standard','Cosy budget room opening onto the garden.',18,'1 Double',2,68.00,8,JSON_ARRAY('Garden view','Wi-Fi'));

-- ---------------------------------------------------------------------
-- tours (8)
-- ---------------------------------------------------------------------
INSERT INTO `tours`
 (`id`,`operator_id`,`destination_id`,`slug`,`title`,`category`,`duration`,`duration_days`,`difficulty`,`group_size`,`max_capacity`,`price`,`discount_pct`,`description`,`itinerary`,`included`,`excluded`,`meeting_point`,`guide_id`,`cover_image`,`rating`,`review_count`,`is_featured`) VALUES
 (1,4,1,'gorilla-trekking-volcanoes','Gorilla Trekking in Volcanoes National Park','Wildlife','1 Day',1,'moderate','Max 8',8,1500.00,0,
  'Trek into the bamboo forest to spend a magical hour with a habituated mountain gorilla family.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Gorilla trekking day','detail','Early briefing, park registration and up to 4 hours trekking. Afternoon community visit.')),
  JSON_ARRAY('Licensed guide','Permit fees','4x4 transport','Lunch','Water'),
  JSON_ARRAY('Flights','Insurance','Tips'), 'Kinigi Park Headquarters',5,
  'assets/img/dest-gorilla.jpg',4.90,214,1),
 (2,4,3,'nyungwe-canopy-walk','Nyungwe Canopy Walk & Chimpanzee Trek','Adventure','2 Days',2,'easy','Max 12',12,480.00,10,
  'Walk 60 metres above the rainforest floor then track habituated chimpanzees at dawn.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Arrival & canopy walk','detail','Transfer to Nyungwe, canopy walkway and evening forest walk.'),JSON_OBJECT('day',2,'title','Chimpanzee trek','detail','Dawn chimpanzee trekking and tea plantation visit.')),
  JSON_ARRAY('Guide','Park fees','Transport','Accommodation','Meals'),
  JSON_ARRAY('Flights','Insurance','Tips'), 'Nyungwe Forest Entrance',5,
  'assets/img/dest-canopy.jpg',4.80,156,1),
 (3,4,2,'lake-kivu-boat-trip','Lake Kivu Boat Trip & Island Hopping','Water','1 Day',1,'easy','Max 15',15,95.00,0,
  'Cruise between the islands, visit hot springs and watch the sunset over Lake Kivu.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Island hopping','detail','Boat cruise to Napoleon and Amahoro islands with swimming stops.')),
  JSON_ARRAY('Boat','Guide','Life jackets','Lunch'),
  JSON_ARRAY('Transport to Rubavu','Insurance'), 'Rubavu Port',5,
  'assets/img/gallery-boat.jpg',4.70,98,1),
 (4,4,4,'akagera-big-five-safari','Akagera Big Five Safari & Boat Cruise','Safari','3 Days',3,'easy','Max 6',6,720.00,5,
  'Game drives and a boat cruise in search of lions, elephants, rhinos, buffalo, leopards and hippos.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Northern circuit','detail','Afternoon game drive in the north.'),JSON_OBJECT('day',2,'title','Full-day safari','detail','Dawn and dusk game drives.'),JSON_OBJECT('day',3,'title','Lake Ihema cruise','detail','Boat cruise and departure.')),
  JSON_ARRAY('Guide','Park fees','Transport','Accommodation','Meals'),
  JSON_ARRAY('Flights','Insurance','Tips'), 'Akagera South Gate',5,
  'assets/img/dest-safari.jpg',4.80,132,1),
 (5,4,5,'kigali-city-tour','Kigali City Tour & Genocide Memorial','Culture','1 Day',1,'easy','Max 20',20,60.00,0,
  'Discover Kigali''s history, markets, art and coffee scene with a local guide.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','City discovery','detail','Genocide Memorial, craft markets, cafe and neighbourhood tour.')),
  JSON_ARRAY('Guide','Transport','Memorial entry'),
  JSON_ARRAY('Lunch','Tips'), 'Kigali City Centre',5,
  'assets/img/gallery-city.jpg',4.60,245,1),
 (6,4,5,'rwanda-coffee-farm-experience','Musanze Coffee Farm Experience','Culture','1 Day',1,'easy','Max 10',10,75.00,0,
  'Harvest, roast and taste Rwanda''s premium arabica on a working coffee farm.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Coffee from cherry to cup','detail','Farm tour, roasting session and tasting.')),
  JSON_ARRAY('Guide','Tasting','Transport'),
  JSON_ARRAY('Lunch','Tips'), 'Musanze Coffee Cooperative',5,
  'assets/img/gallery-coffee.jpg',4.70,64,0),
 (7,4,1,'cultural-village-visit','Iby''iwacu Cultural Village Visit','Culture','1 Day',1,'easy','Max 15',15,85.00,0,
  'Drum, dance and dine with local communities keeping Rwandan traditions alive.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Cultural immersion','detail','Traditional dance, crafts, medicine walk and shared meal.')),
  JSON_ARRAY('Guide','Entry','Performance'),
  JSON_ARRAY('Transport','Tips'), 'Iby''iwacu Village, Musanze',5,
  'assets/img/gallery-intore.jpg',4.80,87,0),
 (8,4,1,'rwanda-highlands-trek','Rwanda Highlands Multi-Day Trek','Adventure','5 Days',5,'hard','Max 8',8,1250.00,15,
  'A five-day trek across the Congo-Nile Divide through remote mountain communities.',
  JSON_ARRAY(JSON_OBJECT('day',1,'title','Start trek','detail','Begin at Mount Karisimbi foothills.'),JSON_OBJECT('day',5,'title','Finish','detail','Descend to Lake Kivu.')),
  JSON_ARRAY('Guide','Porter','Transport','Accommodation','Meals'),
  JSON_ARRAY('Flights','Insurance','Tips'), 'Musanze Trailhead',5,
  'assets/img/dest-hills.jpg',4.90,41,0);

-- ---------------------------------------------------------------------
-- tour_dates
-- ---------------------------------------------------------------------
INSERT INTO `tour_dates` (`tour_id`,`start_date`,`end_date`,`capacity`,`booked`,`seasonal_price`) VALUES
 (1,'2026-06-14','2026-06-14',8,3,NULL),(1,'2026-06-20','2026-06-20',8,0,NULL),
 (1,'2026-07-05','2026-07-05',8,6,NULL),(2,'2026-06-16','2026-06-17',12,2,NULL),
 (3,'2026-06-18','2026-06-18',15,0,NULL),(4,'2026-07-01','2026-07-03',6,1,700.00),
 (5,'2026-06-15','2026-06-15',20,4,NULL);

-- ---------------------------------------------------------------------
-- guides
-- ---------------------------------------------------------------------
INSERT INTO `guides` (`user_id`,`name`,`type`,`languages`,`specialties`,`experience_years`,`daily_rate`,`photo`,`bio`,`rating`) VALUES
 (5,'Emmanuel Niyonsaba','guide',JSON_ARRAY('English','French','Kinyarwanda'),JSON_ARRAY('Gorilla trekking','Birding'),12,80.00,
  NULL,'RDB-certified guide with 12 years in the Virungas.',4.90),
 (NULL,'Claudine Mukamana','guide',JSON_ARRAY('English','Kinyarwanda'),JSON_ARRAY('Culture','City tours'),7,60.00,
  NULL,'Specialist in Kigali culture and history.',4.80),
 (NULL,'Eric Habimana','driver',JSON_ARRAY('English','Kinyarwanda'),JSON_ARRAY('Safari driving','Transfers'),9,70.00,
  NULL,'Experienced 4x4 driver-guide for Akagera safaris.',4.70);

-- ---------------------------------------------------------------------
-- coupons
-- ---------------------------------------------------------------------
INSERT INTO `coupons` (`code`,`type`,`value`,`min_spend`,`usage_limit`,`starts_at`,`expires_at`,`status`) VALUES
 ('WELCOME10','percent',10.00,0,500,'2026-01-01','2026-12-31','active'),
 ('KIVU50K','fixed',50000.00,300000,100,'2026-01-01','2026-12-31','active');

-- ---------------------------------------------------------------------
-- bookings + items + payments (sample)
-- ---------------------------------------------------------------------
INSERT INTO `bookings`
 (`id`,`reference`,`user_id`,`booking_type`,`tour_id`,`hotel_id`,`travel_date`,`check_in`,`check_out`,`guests`,`rooms`,`subtotal`,`tax_total`,`total`,`paid_total`,`currency`,`status`,`payment_status`,`customer_name`,`customer_email`,`customer_phone`,`nationality`) VALUES
 (1,'RX-8F2A1C',6,'tour',1,NULL,'2026-06-14',NULL,NULL,2,1,3000.00,540.00,3540.00,1062.00,'RWF','confirmed','deposit_paid','Jean Uwase','traveler@rwandaexplorer.rw','+250788000006','Rwanda'),
 (2,'RX-3D9B44',6,'hotel',NULL,2,'2026-06-16','2026-06-16','2026-06-18',2,1,620.00,112.00,732.00,0.00,'USD','pending','unpaid','Jean Uwase','traveler@rwandaexplorer.rw','+250788000006','Rwanda'),
 (3,'RX-11AC90',6,'tour',5,NULL,'2025-12-02',NULL,NULL,1,1,60.00,11.00,71.00,71.00,'USD','completed','paid','Jean Uwase','traveler@rwandaexplorer.rw','+250788000006','Rwanda'),
 (4,'RX-77B2E9',1,'tour',4,NULL,'2026-07-01',NULL,NULL,2,1,1440.00,259.20,1699.20,1699.20,'USD','confirmed','paid','Alice Mukamana','alice.mukamana@example.com','+250788000001','Rwanda'),
 (5,'RX-5C1D30',2,'hotel',NULL,1,'2026-06-22','2026-06-22','2026-06-25',2,1,1260.00,226.80,1486.80,0.00,'USD','pending','unpaid','Diane Uwase','diane.uwase@example.com','+250788000002','Rwanda');

INSERT INTO `booking_items` (`booking_id`,`item_type`,`item_id`,`name`,`qty`,`unit_price`,`line_total`) VALUES
 (1,'tour',1,'Gorilla Trekking in Volcanoes National Park',2,1500.00,3000.00),
 (2,'hotel',2,'Kivu Serena Hotel - Standard Lake Room',2,310.00,620.00),
 (3,'tour',5,'Kigali City Tour & Genocide Memorial',1,60.00,60.00),
 (4,'tour',4,'Akagera Big Five Safari & Boat Cruise',2,720.00,1440.00),
 (5,'hotel',1,'Virunga Lodge - Standard Room',3,420.00,1260.00);

INSERT INTO `payments` (`booking_id`,`provider`,`method`,`amount`,`currency`,`status`,`transaction_ref`,`paid_at`) VALUES
 (1,'mtn','Mobile Money',1062.00,'RWF','successful','MOMO-9F2A1C',NOW()),
 (3,'stripe','Card',71.00,'USD','successful','ch_11AC90',NOW()),
 (4,'mtn','Mobile Money',1699.20,'USD','successful','MOMO-77B2E9',NOW());

-- ---------------------------------------------------------------------
-- permits
-- ---------------------------------------------------------------------
INSERT INTO `permits` (`booking_id`,`user_id`,`permit_type`,`full_name`,`email`,`phone`,`nationality`,`passport_no`,`permit_date`,`num_permits`,`price_each`,`status`) VALUES
 (1,6,'gorilla','Jean Uwase','traveler@rwandaexplorer.rw','+250788000006','Rwanda','PC1234567','2026-06-14',2,1500.00,'approved');

-- ---------------------------------------------------------------------
-- reviews
-- ---------------------------------------------------------------------
INSERT INTO `reviews` (`user_id`,`target_type`,`target_id`,`rating`,`title`,`comment`,`author_name`,`status`) VALUES
 (6,'tour',1,5,'Life-changing','The gorilla trek was absolutely magical. Our guide made every moment special.','Jean U.','approved'),
 (NULL,'hotel',1,5,'Unforgettable views','Virunga Lodge exceeded every expectation. The volcano views at dawn are surreal.','Sarah M.','approved'),
 (NULL,'hotel',2,5,'Beach paradise','Kivu Serena is the perfect lakeside escape. Wonderful staff and food.','Amara O.','approved'),
 (NULL,'tour',4,5,'All five in two days','We saw lions, elephants, rhino, buffalo and a leopard. Outstanding.','David C.','approved'),
 (NULL,'destination',3,5,'Canopy walk is unreal','60 metres above the forest - terrifying and beautiful in equal measure.','Lucia M.','approved'),
 (NULL,'tour',5,4,'Great city overview','A perfect introduction to Kigali. The memorial is deeply moving.','Hannah W.','approved'),
 (NULL,'hotel',5,5,'Perfect base for Kigali','Central, quiet and the rooftop pool is wonderful after a long flight.','Peter V.','pending');

-- ---------------------------------------------------------------------
-- blog_posts
-- ---------------------------------------------------------------------
INSERT INTO `blog_posts` (`slug`,`title`,`category`,`excerpt`,`content`,`cover_image`,`read_minutes`,`status`) VALUES
 ('rwanda-visa-requirements','Rwanda Visa Requirements 2026: Everything You Need to Know','Travel Info',
  'Rwanda offers visa-free entry for all Africans and visa-on-arrival for many nationalities.',
  'Rwanda offers visa-free entry for all Africans and visa-on-arrival for many nationalities. This guide covers eligibility, the e-visa portal and the East Africa Tourist Visa, which also covers Uganda and Kenya.\n\nTravellers from most countries can now apply online before travel or get a visa on arrival at Kigali International Airport. Always confirm the latest requirements with the Rwanda Directorate General of Immigration before booking flights.',
  'assets/img/gallery-city.jpg',6,'published'),
 ('best-time-to-visit-rwanda','Best Time to Visit Rwanda: A Month-by-Month Guide','Guides',
  'Dry seasons offer the best gorilla trekking conditions.',
  'The long dry season (June to September) and the short dry season (December to February) offer the best trekking conditions and wildlife viewing. Trails are drier, views clearer and the parks are at their busiest.\n\nGreen seasons bring fewer visitors, lower rates and spectacular birding - and Rwanda is a year-round destination thanks to its mild highland climate.',
  'assets/img/dest-tea.jpg',8,'published'),
 ('rwanda-packing-list','The Ultimate Rwanda Packing List for Gorilla Trekking','Tips',
  'Sturdy boots, long trousers, gardening gloves and a rain jacket.',
  'Sturdy waterproof boots, long trousers, a light rain jacket, gardening gloves for the nettles and a good camera with a fast lens.\n\nPorters can carry your daypack for a small tip - it supports the communities living around the park and keeps your hands free for the steep volcanic soil.',
  'assets/img/dest-hills.jpg',5,'published'),
 ('kinyarwanda-phrases','25 Useful Kinyarwanda Phrases for Travellers','Culture',
  'Muraho! Learn the warm greetings that will open doors.',
  'Muraho means hello and murakoze means thank you. A few words of Kinyarwanda delight locals and open doors everywhere you go.\n\nStart with: Muraho (hello), Murakoze (thank you), Wote? (how are you?), Nishimiye (nice to meet you) and Mwaramutse (good morning).',
  'assets/img/gallery-intore.jpg',4,'published');

-- ---------------------------------------------------------------------
-- gallery
-- ---------------------------------------------------------------------
INSERT INTO `gallery` (`title`,`category`,`url`,`thumb_url`,`sort_order`) VALUES
 ('Mountain gorilla in the Virungas','Wildlife','assets/img/dest-gorilla.jpg',NULL,1),
 ('Lake Kivu hills near Karongi','Landscapes','assets/img/hero-kivu.jpg',NULL,2),
 ('Sunrise over Nyungwe','Landscapes','assets/img/hero-nyungwe.jpg',NULL,3),
 ('Elephants in Akagera','Wildlife','assets/img/dest-safari.jpg',NULL,4),
 ('Kigali city street','Culture','assets/img/gallery-city.jpg',NULL,5),
 ('Bisoke crater view, Volcanoes NP','Landscapes','assets/img/hero-volcanoes.jpg',NULL,6),
 ('Rwandan coffee cherries','Culture','assets/img/gallery-coffee.jpg',NULL,7),
 ('Intore dancers','Culture','assets/img/gallery-intore.jpg',NULL,8),
 ('Swimming on Lake Kivu','Landscapes','assets/img/gallery-boat.jpg',NULL,9),
 ('Tea terraces in the highlands','Landscapes','assets/img/dest-tea.jpg',NULL,10),
 ('Kigali Genocide Memorial','Culture','assets/img/dest-memorial.jpg',NULL,11),
 ('The thousand-hill country','Landscapes','assets/img/dest-hills.jpg',NULL,12);

-- ---------------------------------------------------------------------
-- hero_slides (homepage cinematic hero)
-- ---------------------------------------------------------------------
INSERT INTO `hero_slides` (`title_line1`,`title_line2`,`subtitle`,`image`,`card_title`,`card_desc`,`card_thumb`,`sort_order`) VALUES
 ('VISIT','RWANDA','The Land of a Thousand Hills','assets/img/hero-volcanoes.jpg','Volcanoes NP','Gorilla trekking','assets/img/dest-gorilla.jpg',1),
 ('EXPLORE','LAKE KIVU','Beaches & islands','assets/img/hero-kivu.jpg','Lake Kivu','Beaches & islands','assets/img/hero-kivu.jpg',2),
 ('DISCOVER','NYUNGWE','Canopy walk & chimps','assets/img/hero-nyungwe.jpg','Nyungwe Forest','Canopy walk & chimps','assets/img/dest-canopy.jpg',3),
 ('SAFARI IN','AKAGERA','Big Five safari','assets/img/hero-akagera.jpg','Akagera NP','Big Five safari','assets/img/dest-safari.jpg',4),
 ('EXPERIENCE','KIGALI','Genocide Memorial & city','assets/img/hero-kigali.jpg','Kigali','Genocide Memorial & city','assets/img/dest-memorial.jpg',5);

-- ---------------------------------------------------------------------
-- testimonials
-- ---------------------------------------------------------------------
INSERT INTO `testimonials` (`name`,`country`,`rating`,`text`,`avatar`) VALUES
 ('Sarah Mitchell','United Kingdom',5,'The gorilla trek was life-changing. Rwanda Explorer handled every detail - permits, lodge, guide.',NULL),
 ('Jean-Pierre Dubois','France',5,'Nyungwe canopy walk exceeded all expectations. Our guide knew every bird species by name.',NULL),
 ('Amara Okafor','Nigeria',5,'Everything ran to the minute and mobile-money payment was so easy.',NULL),
 ('David Chen','Singapore',4,'We saw all the Big Five in two days. The Lake Ihema cruise was the highlight.',NULL);

-- ---------------------------------------------------------------------
-- settings
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`group_name`,`key_name`,`value`) VALUES
 ('general','site_name','Rwanda Explorer'),
 ('general','tagline','Best overall for tourist attractions, tour bookings, hotels, and travel guides.'),
 ('general','currency_default','RWF'),
 ('general','currencies','RWF,USD,EUR'),
 ('general','languages','en,fr,rw'),
 ('currency','usd_rate','1300'),
 ('currency','eur_rate','1420'),
 ('payments','commission_pct','10'),
 ('payments','deposit_pct','30'),
 ('payments','gateways','mtn,airtel,stripe,flutterwave,pesapal,cash'),
 ('contact','phone','+250 788 000 000'),
 ('contact','email','hello@rwandaexplorer.rw'),
 ('contact','address','KG 7 Ave, Kigali, Rwanda');

-- ---------------------------------------------------------------------
-- notifications (sample)
-- ---------------------------------------------------------------------
INSERT INTO `notifications` (`user_id`,`type`,`title`,`message`,`channel`) VALUES
 (6,'booking','Booking confirmed','Your gorilla trekking booking RX-8F2A1C is confirmed.','email'),
 (6,'promo','Price drop alert','A hotel on your wishlist is now 15% cheaper.','app');

-- ---------------------------------------------------------------------
-- wishlists + itinerary (sample traveler data)
-- ---------------------------------------------------------------------
INSERT INTO `wishlists` (`user_id`,`target_type`,`target_id`) VALUES
 (6,'hotel',1),(6,'hotel',3),(6,'tour',4),(6,'destination',3);

INSERT INTO `itineraries` (`user_id`,`title`,`share_slug`,`days`,`budget`,`interests`,`items`,`is_public`) VALUES
 (6,'10-Day Grand Rwanda','grand-rwanda-10',10,4500.00,JSON_ARRAY('Wildlife','Culture'),
  JSON_ARRAY(JSON_OBJECT('day',1,'type','destination','name','Kigali','price',0)),1);

-- =====================================================================
--  End of seed
-- =====================================================================
