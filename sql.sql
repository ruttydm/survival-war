-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 02 feb 2014 om 10:21
-- Serverversie: 5.5.34
-- PHP-versie: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `rutgerx99_monstr`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_admins`
--

CREATE TABLE IF NOT EXISTS `km_admins` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_admins`
--

INSERT INTO `km_admins` (`ID`, `username`, `password`) VALUES
(2, 'ruttydm', 'f22c24f8b0c5e704b572f7901d3b0746');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_battlerecords`
--

CREATE TABLE IF NOT EXISTS `km_battlerecords` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `attid` bigint(20) NOT NULL DEFAULT '0',
  `attname` varchar(255) NOT NULL DEFAULT '',
  `result` tinytext NOT NULL,
  `landlost` bigint(20) NOT NULL DEFAULT '0',
  `victimid` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_battlerecords`
--

INSERT INTO `km_battlerecords` (`ID`, `attid`, `attname`, `result`, `landlost`, `victimid`) VALUES
(6, 6, 'ruttydm', 'lost', 0, 14);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_forums`
--

CREATE TABLE IF NOT EXISTS `km_forums` (
  `forumID` bigint(20) NOT NULL AUTO_INCREMENT,
  `forumname` varchar(255) NOT NULL DEFAULT '',
  `timelastpost` varchar(255) NOT NULL DEFAULT '',
  `lastposter` varchar(255) NOT NULL DEFAULT '',
  `numtopics` bigint(20) NOT NULL DEFAULT '0',
  `numposts` bigint(20) NOT NULL DEFAULT '0',
  `descrip` tinytext NOT NULL,
  `forumorder` int(11) NOT NULL DEFAULT '0',
  `realtimelastpost` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`forumID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_forums`
--

INSERT INTO `km_forums` (`forumID`, `forumname`, `timelastpost`, `lastposter`, `numtopics`, `numposts`, `descrip`, `forumorder`, `realtimelastpost`) VALUES
(2, 'New Ideas', 'Fri Nov 22, 2013 18:08:16', 'ruttydm', 5, 6, '', 0, 1385140096),
(3, 'Bugs and problems', '', '', 0, 0, '', 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_messages`
--

CREATE TABLE IF NOT EXISTS `km_messages` (
  `msgid` bigint(20) NOT NULL AUTO_INCREMENT,
  `posterid` bigint(20) NOT NULL DEFAULT '0',
  `parentid` bigint(20) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL DEFAULT '0',
  `numreplies` bigint(20) NOT NULL DEFAULT '0',
  `realtime` varchar(255) NOT NULL DEFAULT '',
  `subject` tinytext NOT NULL,
  `message` mediumtext NOT NULL,
  `lastreplied` varchar(255) NOT NULL DEFAULT '',
  `forumparent` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`msgid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_messages`
--

INSERT INTO `km_messages` (`msgid`, `posterid`, `parentid`, `time`, `numreplies`, `realtime`, `subject`, `message`, `lastreplied`, `forumparent`) VALUES
(1, 5, 0, 1384543218, 0, 'Fri Nov 15, 2013 20:20:18', 'test', 'test', '', 1),
(2, 10, 0, 1384600717, 0, 'Sat Nov 16, 2013 12:18:37', 'test', 'test\\r\\n\\r\\n', '', 2),
(3, 9, 0, 1384704919, 0, 'Sun Nov 17, 2013 17:15:19', 'probleem', 'Rutger er is een probleem! als je op de site komt bij buy land en zo is er iets mis, die bovenste dingens staan over de streep!', '', 2),
(4, 9, 0, 1384705042, 0, 'Sun Nov 17, 2013 17:17:22', 'nog een probleem', 'als je op playerlist komt komt er error', '', 2),
(5, 6, 0, 1385139748, 0, 'Fri Nov 22, 2013 18:02:28', 'test', 'tttttttest', '', 2),
(6, 14, 0, 1385140096, 1, 'Fri Nov 22, 2013 18:08:16', 'chat', '', 'ruttydm', 2),
(7, 6, 6, 1385140096, 0, 'Fri Nov 22, 2013 18:08:16', 'chat', 'http://net.tutsplus.com/tutorials/javascript-ajax/how-to-create-a-simple-web-based-chat-application/', '', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_monsters`
--

CREATE TABLE IF NOT EXISTS `km_monsters` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `skill` bigint(20) NOT NULL DEFAULT '0',
  `pointsifkilled` bigint(255) NOT NULL DEFAULT '0',
  `goldworth` bigint(20) NOT NULL DEFAULT '0',
  `energycost` int(11) NOT NULL,
  `image` varchar(3000) NOT NULL DEFAULT 'http://images.puella-magi.net/thumb/2/27/No_Image_Wide.svg/800px-No_Image_Wide.svg.png?20110202071158',
  `continent` varchar(30) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_monsters`
--

INSERT INTO `km_monsters` (`ID`, `name`, `skill`, `pointsifkilled`, `goldworth`, `energycost`, `image`, `continent`) VALUES
(9, 'Spider', 90, 9, 9, 6, 'http://upload.wikimedia.org/wikipedia/commons/5/59/Mouse_spider.jpg', ''),
(7, 'Mouse', 15, 1, 1, 1, 'http://cdn.orkin.com/images/rodents/deer-mouse-illustration_360x244.jpg', ''),
(11, 'Deer', 750, 75, 75, 50, 'http://upload.wikimedia.org/wikipedia/commons/1/16/Mule_Deer_in_snow.jpg', ''),
(12, 'Bear', 1500, 150, 150, 100, 'http://inserbia.info/news/wp-content/uploads/2013/05/grizzly.jpg', ''),
(13, 'Rabbit', 450, 45, 45, 30, 'http://upload.wikimedia.org/wikipedia/commons/3/3b/Rabbit_in_montana.jpg', ''),
(14, 'Hog', 1200, 120, 120, 80, 'http://www.captainwoodygore.com/blog/wp-content/uploads/2012/07/ferral-Hog-11.jpg', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `km_users`
--

CREATE TABLE IF NOT EXISTS `km_users` (
  `ID` bigint(21) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0',
  `playername` varchar(15) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `skillpts` bigint(21) NOT NULL DEFAULT '0',
  `dead` varchar(255) NOT NULL DEFAULT '',
  `killer` varchar(255) NOT NULL DEFAULT '',
  `numberattck` bigint(20) NOT NULL DEFAULT '0',
  `justattacked` int(4) NOT NULL DEFAULT '0',
  `honor` int(11) NOT NULL DEFAULT '0',
  `lastaction` bigint(20) NOT NULL DEFAULT '0',
  `gold` bigint(20) NOT NULL DEFAULT '0',
  `land` bigint(20) NOT NULL DEFAULT '0',
  `offarmy` bigint(20) NOT NULL DEFAULT '0',
  `dffarmy` bigint(6) NOT NULL DEFAULT '0',
  `science` bigint(20) NOT NULL DEFAULT '0',
  `validated` int(11) NOT NULL DEFAULT '0',
  `validkey` varchar(255) NOT NULL DEFAULT '',
  `numturns` bigint(20) NOT NULL DEFAULT '15',
  `tsgone` bigint(20) NOT NULL DEFAULT '0',
  `oldtime` bigint(20) NOT NULL DEFAULT '0',
  `lasttime` bigint(20) NOT NULL DEFAULT '0',
  `online` int(1) NOT NULL DEFAULT '0',
  `energycron` int(11) NOT NULL,
  `continent` varchar(30) NOT NULL,
  `ip` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Gegevens worden uitgevoerd voor tabel `km_users`
--

INSERT INTO `km_users` (`ID`, `status`, `playername`, `password`, `email`, `skillpts`, `dead`, `killer`, `numberattck`, `justattacked`, `honor`, `lastaction`, `gold`, `land`, `offarmy`, `dffarmy`, `science`, `validated`, `validkey`, `numturns`, `tsgone`, `oldtime`, `lasttime`, `online`, `energycron`, `continent`, `ip`) VALUES
(6, 0, 'ruttydm', 'f22c24f8b0c5e704b572f7901d3b0746', 'rutger.demaeyer@gmail.com', 632, 'no', 'hannydm', 0, 0, 2, 1385234304, 1650, 20, 7, 6, 36, 1, 'b1cdacb71ce717edd96325870a20b504', 100, 1390412831, 1390410540, 1390412831, 0, 1, '', ''),
(14, 0, 'hannydm', 'c35b49da1b6dd679a48d67f456bc7aaf', 'hanja.demaeyer@gmail.com', 19, 'no', 'ruttydm', 0, 0, 2, 0, 10791, 6, 30, 30, 0, 1, '7750609a2a555cec737d021cd3703383', 100, 1385229417, 1385140783, 1385229419, 0, 32834, '', ''),
(16, 0, 'kilghard', '81dc9bdb52d04dc20036dbd8313ed055', 'janssenjonas2@gmail.com', 15, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '5b3f5ade5e76090c3503daa5d78d21d2', 100, 0, 0, 0, 0, 41474, '', ''),
(15, 0, 'profound', '010fcf779f509c384ba2c51a8a179e11', 'jadrandub@gmail.com', 15, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'bcd23aa6fe895409c937f3fafd25ae60', 100, 0, 0, 0, 0, 41474, '', ''),
(17, 3, 'ruttydm', 'f22c24f8b0c5e704b572f7901d3b0746', '', 632, '', '', 0, 0, 0, 0, 12590, 0, 0, 0, 0, 1, '', 100, 0, 0, 0, 0, 41474, '', ''),
(18, 0, 'testen', '7651320fc8fd972af966e40752ddcecc', 'testen@testen.be', 15, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'b1e9d3c44f17e72bc18fed7c48a7b2c0', 100, 0, 0, 0, 0, 41474, '', ''),
(19, 0, 'testen1', '018d8f6fdf495d4fe4c6e3ec6db37ac0', 'robin.haentjens@live.be', 15, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'b1e9d3c44f17e72bc18fed7c48a7b2c0', 100, 0, 0, 0, 0, 41473, '', ''),
(20, 0, 'tester', 'f5d1278e8109edd94e1e4197e04873b9', 'c2397644@drdrb.com', 15, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'a38ae2dd1995daeeb9847dd17f94f34f', 100, 0, 0, 0, 0, 41473, '', ''),
(22, 0, 'homokont', '58bc83d1d9acdb81b61b09778a5a6b19', 'info@streetgun.net', 0, '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '4c3a24a93e1a73ecfc8fd1fed1f6bd6e', 100, 0, 0, 0, 0, 0, '', '82.95.130.161');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
