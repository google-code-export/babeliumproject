--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`ID`,`name`,`password`,`email`,`realName`,`realSurname`,`creditCount`,`joiningDate`,`active`,`activation_hash`,`isAdmin`) VALUES 
(1,'guest1','7ca6774b43437f228048ae4451081963bc84802c','guest1@mailinator.com','Guest1','',200,'2009-07-02 12:30:00',1,'',0),
(2,'guest2','4eff1c28f92bb604596e75d2c98bf7085ac685c4','guest2@mailinator.com','Guest2','',200,'2009-07-02 12:30:00',0,'',0);


--
-- Dumping data for table `user_languages`
--

LOCK TABLES `user_languages` WRITE;
/*!40000 ALTER TABLE `user_languages` DISABLE KEYS */;
INSERT INTO `user_languages` (`id`,`fk_user_id`,`language`,`level`,`positives_to_next_level`,`purpose`) VALUES
(1,1,'es_ES',7,15,'evaluate'),
(2,1,'en_US',5,15,'practice'),
(3,2,'en_US',7,15,'evaluate'),
(4,2,'es_ES',5,15,'practice');
/*!40000 ALTER TABLE `user_languages` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `exercise`
--

LOCK TABLES `exercise` WRITE;
/*!40000 ALTER TABLE `exercise` DISABLE KEYS */;
INSERT INTO `exercise` (`id`,`name`,`description`,`source`,`language`,`fk_user_id`,`tags`,`title`,`thumbnail_uri`,`adding_date`,`duration`,`status`,`filehash`,`fk_transcription_id`,`license`,`reference`) VALUES 
(5,'tdes_1065_qa','Repeat phrases and then talk to Sarah','Red5','en_US',1,'daily, english, show','The Daily English Show #1065 Fragment','tdes_1065_qa.jpg','2010-03-08 12:10:00',43,'Available','161abc5e831c545305f55f4139fd4799',NULL,'cc-by','http://www.thedailyenglishshow.com'),
(6,'tdes_1170_qa','Repeat phrases and then talk to Sarah','Red5','en_US',1,'daily, english, show','The Daily English Show #1170 Fragment','tdes_1170_qa.jpg','2010-03-08 12:10:00',52,'Available','38b99457f8cd8af5b56728c5e2f0485b',NULL,'cc-by','http://www.thedailyenglishshow.com'),
(7,'tdes_1179_qa','Repeat phrases and then talk to Sarah','Red5','en_US',1,'daily, english, show','The Daily English Show #1179 Fragment','tdes_1179_qa.jpg','2010-03-08 12:10:00',30,'Available','4fe59e622c208b53dc4e61cfdcb7b2a8',NULL,'cc-by','http://www.thedailyenglishshow.com'),
(8,'tdes_1183_qa','Repeat phrases and then talk to Sarah','Red5','en_US',1,'daily, english, show','The Daily English Show #1183 Fragment','tdes_1183_qa.jpg','2010-03-08 12:10:00',40,'Available','2f8d1bde45ae7a7d303d663bcdf2ae8c',NULL,'cc-by','http://www.thedailyenglishshow.com'),
(9,'tdes_1187_qa','Repeat phrases and then talk to Sarah','Red5','en_US',1,'daily, english, show','The Daily English Show #1187 Fragment','tdes_1187_qa.jpg','2010-03-08 12:10:00',57,'Available','a0cd07a3ac94d2dbf77ca1c02c6278cc',NULL,'cc-by','http://www.thedailyenglishshow.com');
/*!40000 ALTER TABLE `exercise` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `subtitle`
--

LOCK TABLES `subtitle` WRITE;
/*!40000 ALTER TABLE `subtitle` DISABLE KEYS */;
INSERT INTO `subtitle` (`id`,`fk_exercise_id`,`fk_user_id`,`language`,`translation`,`adding_date`) VALUES 
(6,6,1,'en_US',0,'2010-06-04 23:23:11'),
(7,7,1,'en_US',0,'2010-06-04 23:23:11'),
(8,8,1,'en_US',0,'2010-06-04 23:23:11'),
(9,9,1,'en_US',0,'2010-06-04 23:23:11'),
(23,5,1,'en_US',0,'2010-06-04 23:23:11');
/*!40000 ALTER TABLE `subtitle` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `exercise_level`
--

LOCK TABLES `exercise_level` WRITE;
/*!40000 ALTER TABLE `exercise_level` DISABLE KEYS */;
INSERT INTO `exercise_level` (`id`,`fk_exercise_id`,`fk_user_id`,`suggested_level`,`suggest_date`) VALUES 
(1,5,1,4,'2010-07-29 17:49:46'),
(2,6,1,4,'2010-07-29 17:49:46'),
(3,7,1,4,'2010-07-29 17:49:46'),
(4,8,1,4,'2010-07-29 17:49:46'),
(5,9,1,4,'2010-07-29 17:49:46');
/*!40000 ALTER TABLE `exercise_level` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `exercise_role`
--

LOCK TABLES `exercise_role` WRITE;
/*!40000 ALTER TABLE `exercise_role` DISABLE KEYS */;
INSERT INTO `exercise_role` (`id`,`fk_exercise_id`,`fk_user_id`,`character_name`) VALUES 
(11,6,1,'NPC'),
(12,6,1,'Yourself'),
(13,7,1,'NPC'),
(14,7,1,'Yourself'),
(15,8,1,'NPC'),
(16,8,1,'Yourself'),
(17,9,1,'NPC'),
(18,9,1,'Yourself'),
(19,23,1,'NPC'),
(20,23,1,'Yourself');
/*!40000 ALTER TABLE `exercise_role` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `subtitle_line`
--

LOCK TABLES `subtitle_line` WRITE;
/*!40000 ALTER TABLE `subtitle_line` DISABLE KEYS */;
INSERT INTO `subtitle_line` (`id`,`fk_subtitle_id`,`show_time`,`hide_time`,`text`,`fk_exercise_role_id`) VALUES 
(32,6,0.3,2.307,'Oh, it\'s going that well, huh?',11),
(33,6,2.507,4.9,'Oh, it\'s going that well, huh?',12),
(34,6,5.1,8.023,'When do I get to meet the phantom physician?',11),
(35,6,8.223,10.7,'When do I get to meet the phantom physician?',12),
(36,6,10.9,12.205,'You guys got plans tonight?',11),
(37,6,12.405,13.9,'You guys got plans tonight?',12),
(38,6,14.1,15.41,'You know what you should do?',11),
(39,6,15.61,17,'You know what you should do?',12),
(40,6,17.2,19.321,'You should fly up and surprise him.',11),
(41,6,19.521,21.9,'You should fly up and surprise him.',12),
(42,6,22.1,23.232,'Yeah, why not?',11),
(43,6,23.432,25,'Yeah, why not?',12),
(44,6,25.2,26.716,'He\'s just the first decent guy I dated in a long time.',11),
(45,6,26.916,31.2,'Oh, it\'s going that well, huh?',12),
(46,6,31.4,34.831,'I\'m so sick of dating. I\'m so jealous of you guys.',11),
(47,6,35.031,37.6,'When do I get to meet the phantom physician?',12),
(48,6,37.8,38.608,'I think soon.',11),
(49,6,38.808,40.7,'You guys got plans tonight?',12),
(50,6,40.9,45.828,'No, nothing. Just... he has to go to San Francisco so we\'re gonna talk on the ph',11),
(51,6,46.028,49.9,'You know what you should do? You should fly up and surprise him.',12),
(52,6,50.1,50.808,'You think so?',11),
(53,6,51.008,52.4,'Yeah, why not?',12),

(54,7,0.3,1.605,'OK, now what?',13),
(55,7,1.805,3.1,'OK, now what?',14),
(56,7,3.3,5.917,'OK, that\'s fair. On three?',13),
(57,7,6.117,7.6,'OK, that\'s fair. On three?',14),
(58,7,7.8,10.129,'One, two, three.',13),
(59,7,10.329,12.1,'One, two, three.',14),
(60,7,12.3,13.505,'I threw paper!',13),
(61,7,13.705,15.1,'I threw paper!',14),
(62,7,17.114,17.7,'OK, now what?',14),
(63,7,17.9,19.722,'Rock-paper-scissors for it',13),
(64,7,19.922,21.6,'OK, that\'s fair. On three?',14),
(65,7,21.8,23.031,'Yeah.',13),
(66,7,23.231,27.51,'One, two, three.',14),
(67,7,27.71,28.7,'I threw paper!',14),
(68,7,28.9,29.3,'I threw a rock!',13),

(69,8,0.3,1.806,'How was your day?',15),
(70,8,2.006,3.1,'How was your day?',16),
(71,8,3.3,4.012,'What about?',15),
(72,8,4.212,5.3,'What about?',16),
(73,8,5.5,6.92,'What\'s gonna happen to it?',15),
(74,8,7.12,9,'What\'s gonna happen to it?',16),
(75,8,9.2,10.831,'So they\'re gonna get rid of it?',15),
(76,8,11.031,13.7,'So they\'re gonna get rid of it?',16),
(77,8,14.507,16.1,'How was your day?',16),
(78,8,16.3,19.923,'Good! I went to a protest this afternoon',15),
(79,8,20.123,21.2,'What about?',16),
(80,8,21.4,26.106,'It was about protecting national public service radio broadcasting',15),
(81,8,26.306,27.9,'What\'s gonna happen to it?',16),
(82,8,28.1,31.923,'Well... the government wants to save money...',15),
(83,8,32.123,33.3,'So they\'re gonna get rid of it?',16),
(84,8,33.5,39.8,'Not quite, but they suggested stuff like introducing advertising, which would re',15),

(85,9,0.3,2.006,'What are you up to this weekend?',17),
(86,9,2.206,3.6,'What are you up to this weekend?',18),
(87,9,3.8,4.613,'Where\'s that?',17),(88,9,4.813,5.8,'Where\'s that?',18),
(89,9,6,8.123,'Oh yeah, I know where the Albert Park is.',17),
(90,9,8.323,10.8,'Oh yeah, I know where the Albert Park is.',18),
(91,9,11,13.906,'That\'s were the lantern festival was last week.',17),
(92,9,14.106,17.4,'That\'s were the lantern festival was last week.',18),
(93,9,17.6,19.12,'Yeah, did you?',17),
(94,9,19.32,20.8,'Yeah, did you?',18),
(95,9,21,23.7,'Yeah. What day did you go?',17),
(96,9,23.9,26.5,'Yeah. What day did you go?',18),
(97,9,27.309,29.1,'What are you up to this weekend?',18),
(98,9,29.3,34.229,'Ah, I\'m not sure yet. I might go and check out the concert on sunday.',17),
(99,9,34.429,35.5,'Where\'s that?',18),
(100,9,35.7,39.611,'It\'s in Albert Park which is the park near the university.',17),
(101,9,39.811,45.2,'Oh yeah, I know where the Albert Park is. That\'s were the lantern festival was l',18),
(102,9,45.4,48.201,'Yeah that\'s the one. Did you go to that festival?',17),
(103,9,48.401,49.6,'Yeah, did you?',18),
(104,9,49.8,52.012,'Yeah, it was awesome, wasn\'t it?',17),
(105,9,52.212,53.8,'Yeah. What day did you go?',18),
(106,9,54,57.1,'Am, Sunday I think it was. Yeah, yeah, Sunday night.',17),

(210,23,0.3,1.2,'What\'s that?',47),
(211,23,1.4,2.9,'What\'s that?',48),
(212,23,3.1,5.22,'Can\'t you just use your sleeve?',47),
(213,23,5.42,8.2,'Can\'t you just use your sleeve?',48),
(214,23,8.4,9.23,'Who gave it to you?',47),
(215,23,9.43,11.5,'Who gave it to you?',48),
(216,23,11.7,14.31,'Look I got a present',47),
(217,23,14.51,15.6,'What\'s that?',48),
(218,23,15.8,18.52,'It\'s a \"keitai kurina\".',47),
(219,23,18.72,19.6,'What\'s that?',48),
(220,23,19.8,28.51,'Well, \"keitai\" means cell-phone and this has material on the back of it and...',47),
(221,23,28.71,30.9,'Can\'t you just use your sleeve?',48),
(222,23,31.1,32.83,'Well... yeah, but...',47),
(223,23,33.03,34.3,'Who gave it to you?',48),
(224,23,34.5,43,'Am Anichke. They made a new website, and they used a few seconds of one of my videos. So... they sent me, this.',47);
/*!40000 ALTER TABLE `subtitle_line` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `motd`
--

LOCK TABLES `motd` WRITE;
/*!40000 ALTER TABLE `motd` DISABLE KEYS */;
INSERT INTO `motd` (`id`,`title`,`message`,`resource`,`displaydate`,`displaywhenloggedin`,`code`,`language`) VALUES 
(1,'Record a video-exercise  as many times as you want','After recording a video-exercise you can watch or redo it again  before publishing it?\rJust click the Watch Simultaneously or Watch Response button. Whenever you are confident\rwith your work,  click “Save Response” Button in order to be evaluated.','/img/motd1.png','2010-10-01 00:00:00',0,'1','en_US'),
(2,'Did you know that you can dub your favourite actor','or actress? Do you feel lucky, punk?','/img/motd2.png','2010-10-04 00:00:00',0,'2','en_US'),
(3,'Did you know that you can report an inappropiate video?','Users can report inappropiate videos, choosing the reason for banning among a frequently used reasons list.','/img/motd3.png','2010-10-03 00:00:00',0,'3','en_US'),
(4,'Did you know that you can follow us on Twitter?','Just follow the @babelium user','/img/motd6.png','2010-10-05 00:00:00',0,'4','en_US');
/*!40000 ALTER TABLE `motd` ENABLE KEYS */;
UNLOCK TABLES;


