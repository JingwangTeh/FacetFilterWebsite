-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2017 at 03:33 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_assessment_major7`
--

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `movie_index` int(16) NOT NULL,
  `movie_title` varchar(64) NOT NULL,
  `imdb_Ref_ID` varchar(64) NOT NULL,
  `released_date` varchar(16) NOT NULL,
  `released_date_interval` varchar(64) DEFAULT NULL,
  `movie_type` varchar(64) NOT NULL,
  `content_rating` varchar(64) NOT NULL,
  `movie_genre` varchar(255) NOT NULL,
  `total_season` int(16) NOT NULL,
  `total_season_interval` varchar(64) DEFAULT NULL,
  `movie_duration` int(16) NOT NULL,
  `movie_duration_interval` varchar(64) DEFAULT NULL,
  `movie_language` varchar(64) NOT NULL,
  `movie_country` varchar(64) NOT NULL,
  `meta_score` int(3) NOT NULL,
  `meta_score_interval` varchar(64) DEFAULT NULL,
  `imdb_rating` float NOT NULL,
  `imdb_rating_interval` varchar(64) DEFAULT NULL,
  `imdb_votes` int(16) NOT NULL,
  `imdb_votes_interval` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`movie_index`, `movie_title`, `imdb_Ref_ID`, `released_date`, `released_date_interval`, `movie_type`, `content_rating`, `movie_genre`, `total_season`, `total_season_interval`, `movie_duration`, `movie_duration_interval`, `movie_language`, `movie_country`, `meta_score`, `meta_score_interval`, `imdb_rating`, `imdb_rating_interval`, `imdb_votes`, `imdb_votes_interval`) VALUES
(1, 'The Shawshank Redemption', 'tt0111161', '14/10/1994', '1990+ - 2000', 'movie', 'R', 'Crime,Drama', 0, '0 - 1', 142, '120+', 'English', 'USA', 80, '75+ - 85', 9.3, '8.5+ - 9.5', 1803191, '1,000,000+'),
(2, 'The Godfather', 'tt0068646', '24/03/1972', '1970+ - 1980', 'movie', 'R', 'Crime,Drama', 0, '0 - 1', 175, '120+', 'English,Italian,Latin', 'USA', 100, '95+ - 100', 9.2, '8.5+ - 9.5', 1227935, '1,000,000+'),
(3, 'The Godfather: Part II', 'tt0071562', '20/12/1974', '1970+ - 1980', 'movie', 'R', 'Crime,Drama', 0, '0 - 1', 202, '120+', 'English,Italian,Spanish,Latin,Sicilian', 'USA', 80, '75+ - 85', 9, '8.5+ - 9.5', 845231, '500,000+ - 1,000,000'),
(4, 'The Dark Knight', 'tt0468569', '18/07/2008', '2005+ - 2010', 'movie', 'PG-13', 'Action,Crime,Drama', 0, '0 - 1', 152, '120+', 'English,Mandarin', 'USA,UK', 82, '75+ - 85', 9, '8.5+ - 9.5', 1780245, '1,000,000+'),
(6, '12 Angry Men', 'tt0050083', '01/04/1957', '0 - 1970', 'movie', 'APPROVED', 'Crime,Drama', 0, '0 - 1', 96, '60+ - 120', 'English', 'USA', 0, '0 - 10', 8.9, '8.5+ - 9.5', 485954, '100,000+ - 500,000'),
(7, 'Schindler\'s List', 'tt0108052', '04/02/1994', '1990+ - 2000', 'movie', 'R', 'Biography,Drama,History', 0, '0 - 1', 195, '120+', 'English,Hebrew,German,Polish', 'USA', 93, '85+ - 95', 8.9, '8.5+ - 9.5', 925979, '500,000+ - 1,000,000'),
(8, 'Pulp Fiction', 'tt0110912', '14/10/1994', '1990+ - 2000', 'movie', 'R', 'Crime,Drama', 0, '0 - 1', 154, '120+', 'English,Spanish,French', 'USA', 94, '85+ - 95', 8.9, '8.5+ - 9.5', 1409235, '1,000,000+'),
(9, 'The Lord of the Rings: The Return of the King', 'tt0167260', '17/12/2003', '2000+ - 2005', 'movie', 'PG-13', 'Adventure,Drama,Fantasy', 0, '0 - 1', 201, '120+', 'English,Quenya,OldEnglish,Sindarin', 'USA,NewZealand', 94, '85+ - 95', 8.9, '8.5+ - 9.5', 1289043, '1,000,000+'),
(10, 'The Good, the Bad and the Ugly', 'tt0060196', '29/12/1967', '0 - 1970', 'movie', 'APPROVED', 'Western', 0, '0 - 1', 161, '120+', 'Italian', 'Italy,Spain,WestGermany,USA', 90, '85+ - 95', 8.9, '8.5+ - 9.5', 532413, '500,000+ - 1,000,000'),
(11, 'Star Wars: Episode V - The Empire Strikes Back', 'tt0080684', '20/06/1980', '1970+ - 1980', 'movie', 'PG', 'Action,Adventure,Fantasy', 0, '0 - 1', 124, '120+', 'English', 'USA', 81, '75+ - 85', 8.8, '8.5+ - 9.5', 897240, '500,000+ - 1,000,000'),
(12, 'Zootopia', 'tt2948356', '04/03/2016', '2015+', 'movie', 'PG', 'Animation,Adventure,Comedy', 0, '0 - 1', 108, '60+ - 120', 'English', 'USA', 78, '75+ - 85', 8.1, '7.5+ - 8.5', 291412, '100,000+ - 500,000'),
(13, 'Your Name', 'tt5311514', '07/04/2017', '2015+', 'movie', 'PG', 'Animation,Drama,Fantasy', 0, '0 - 1', 106, '60+ - 120', 'Japanese,Chinese', 'Japan', 79, '75+ - 85', 8.6, '8.5+ - 9.5', 23903, '10,000+ - 25,000'),
(14, 'Frozen', 'tt2294629', '27/11/2013', '2010+ - 2015', 'movie', 'PG', 'Animation,Adventure,Comedy', 0, '0 - 1', 102, '60+ - 120', 'English,Icelandic', 'USA', 74, '65+ - 75', 7.5, '6.5+ - 7.5', 449013, '100,000+ - 500,000'),
(15, 'Anohana: The Flower We Saw That Day', 'tt1913273', '14/04/2011', '2010+ - 2015', 'series', 'TV-PG', 'Animation,Adventure,Drama', 1, '0 - 1', 23, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 8.4, '7.5+ - 8.5', 3079, '2,500+ - 5,000'),
(17, 'Spirited Away', 'tt0245429', '28/03/2003', '2000+ - 2005', 'movie', 'PG', 'Animation,Adventure,Family', 0, '0 - 1', 125, '120+', 'Japanese', 'Japan', 94, '85+ - 95', 8.6, '8.5+ - 9.5', 456251, '100,000+ - 500,000'),
(18, 'Puella Magi Madoka Magica', 'tt1773185', '06/01/2011', '2010+ - 2015', 'series', 'TV-14', 'Animation,Fantasy,Mystery', 1, '0 - 1', 24, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 8.4, '7.5+ - 8.5', 3704, '2,500+ - 5,000'),
(19, 'The Melancholy of Haruhi Suzumiya', 'tt0816407', '02/04/2006', '2005+ - 2010', 'series', 'TV-14', 'Animation,Comedy,Sci-Fi', 2, '1+ - 3', 24, '20+ - 30', 'Japanese', 'Japan,Germany', 0, '0 - 10', 8, '7.5+ - 8.5', 5561, '5,000+ - 10,000'),
(20, 'Lucky Star', 'tt1086236', '08/04/2007', '2005+ - 2010', 'series', 'TV-PG', 'Animation,Comedy', 0, '0 - 1', 25, '20+ - 30', 'Japanese', 'Japan,Germany', 0, '0 - 10', 7.7, '7.5+ - 8.5', 1297, '1,000+ - 2,500'),
(21, 'Akira', 'tt0094625', '16/07/1988', '1980+ - 1990', 'movie', 'R', 'Animation,Action,Drama', 0, '0 - 1', 124, '120+', 'Japanese', 'Japan', 0, '0 - 10', 8.1, '7.5+ - 8.5', 113664, '100,000+ - 500,000'),
(22, 'My Ordinary Life', 'tt2098308', '03/04/2011', '2010+ - 2015', 'series', 'N/A', 'Animation,Comedy', 0, '0 - 1', 0, '0 - 2', 'Japanese,Malay,Indonesian,English', 'Japan', 0, '0 - 10', 8.4, '7.5+ - 8.5', 733, '500+ - 750'),
(25, 'Clannad', 'tt1118804', '04/10/2007', '2005+ - 2010', 'series', 'TV-PG', 'Animation,Comedy,Drama', 1, '0 - 1', 30, '20+ - 30', 'Japanese,Mandarin,Russian,Korean,Portuguese', 'Japan', 0, '0 - 10', 8.1, '7.5+ - 8.5', 4725, '2,500+ - 5,000'),
(26, 'A New Title Update', 'ID123', '15/5/2017', '2015+', 'movie', 'PG', 'animation,drama', 1, '0 - 1', 24, '20+ - 30', 'English', 'Australia', 100, '95+ - 100', 10, '9.5+ - 10.0', 10, '0 - 50'),
(27, 'Guardians of the Galaxy Vol. 2', 'tt3896198', '05/05/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Sci-Fi', 0, '0 - 1', 136, '120+', 'English', 'USA', 67, '65+ - 75', 8.2, '7.5+ - 8.5', 80269, '50,000+ - 100,000'),
(30, 'Alien: Covenant', 'tt2316204', '19/05/2017', '2015+', 'movie', 'N/A', 'Sci-Fi,Thriller', 0, '0 - 1', 0, '0 - 2', 'English', 'USA,UK', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(31, 'Baahubali 2: The Conclusion', 'tt4849438', '28/04/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Drama', 0, '0 - 1', 0, '0 - 2', 'Telugu,Tamil,Hindi,Malayalam', 'India', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(32, 'The Dark Tower', 'tt1648190', '04/08/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Fantasy', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(33, 'The Fate of the Furious', 'tt4630562', '14/04/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Crime', 0, '0 - 1', 136, '120+', 'English', 'USA', 56, '50+ - 65', 7.3, '6.5+ - 7.5', 56966, '50,000+ - 100,000'),
(34, 'Get Out', 'tt5052448', '24/02/2017', '2015+', 'movie', 'R', 'Horror,Mystery', 0, '0 - 1', 104, '60+ - 120', 'English', 'USA', 84, '75+ - 85', 8, '7.5+ - 8.5', 77121, '50,000+ - 100,000'),
(35, 'Guardians of the Galaxy', 'tt2015381', '01/08/2014', '2010+ - 2015', 'movie', 'PG-13', 'Action,Adventure,Sci-Fi', 0, '0 - 1', 121, '120+', 'English', 'USA,UK', 76, '75+ - 85', 8.1, '7.5+ - 8.5', 746571, '500,000+ - 1,000,000'),
(36, 'The Circle', 'tt4287320', '28/04/2017', '2015+', 'movie', 'PG-13', 'Drama,Sci-Fi,Thriller', 0, '0 - 1', 110, '60+ - 120', 'English', 'UnitedArabEmirates,USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(37, 'Wonder Woman', 'tt0451279', '02/06/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Fantasy', 0, '0 - 1', 141, '120+', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(38, 'Pirates of the Caribbean: Dead Men Tell No Tales', 'tt1790809', '26/05/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Comedy', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(39, 'Split', 'tt4972582', '20/01/2017', '2015+', 'movie', 'PG-13', 'Horror,Thriller', 0, '0 - 1', 117, '60+ - 120', 'English', 'USA', 62, '50+ - 65', 7.4, '6.5+ - 7.5', 110172, '100,000+ - 500,000'),
(40, '47 Meters Down', 'tt2932536', '16/06/2017', '2015+', 'movie', 'PG-13', 'Horror,Thriller', 0, '0 - 1', 87, '60+ - 120', 'English', 'UK', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(41, 'King Arthur: Legend of the Sword', 'tt1972591', '12/05/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Drama', 0, '0 - 1', 126, '120+', 'English', 'UK,Australia,USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(42, 'Baywatch', 'tt1469304', '25/05/2017', '2015+', 'movie', 'R', 'Comedy', 0, '0 - 1', 119, '60+ - 120', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(43, 'It Comes at Night', 'tt4695012', '09/06/2017', '2015+', 'movie', 'R', 'Horror,Mystery', 0, '0 - 1', 97, '60+ - 120', 'English', 'USA', 0, '0 - 10', 8.5, '7.5+ - 8.5', 79, '50+ - 250'),
(44, 'Ghost in the Shell', 'tt1219827', '31/03/2017', '2015+', 'movie', 'PG-13', 'Action,Crime,Drama', 0, '0 - 1', 107, '60+ - 120', 'English,Japanese', 'USA,UK,India,China,Canada', 52, '50+ - 65', 6.8, '6.5+ - 7.5', 44127, '25,000+ - 50,000'),
(45, 'Star Wars: The Last Jedi', 'tt2527336', '15/12/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Fantasy', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(46, 'Beauty and the Beast', 'tt2771200', '17/03/2017', '2015+', 'movie', 'PG', 'Family,Fantasy,Musical', 0, '0 - 1', 129, '120+', 'English', 'USA,UK', 65, '50+ - 65', 7.8, '7.5+ - 8.5', 79791, '50,000+ - 100,000'),
(47, 'Sing', 'tt3470600', '21/12/2016', '2015+', 'movie', 'PG', 'Animation,Comedy,Family', 0, '0 - 1', 108, '60+ - 120', 'English,Japanese,Ukrainian', 'USA', 59, '50+ - 65', 7.2, '6.5+ - 7.5', 52886, '50,000+ - 100,000'),
(48, 'Kingsman: The Golden Circle', 'tt4649466', '29/09/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Comedy', 0, '0 - 1', 0, '0 - 2', 'English', 'UK,USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(49, 'Logan', 'tt3315342', '03/03/2017', '2015+', 'movie', 'R', 'Action,Drama,Sci-Fi', 0, '0 - 1', 141, '120+', 'English,Spanish', 'USA,Canada,Australia', 77, '75+ - 85', 8.4, '7.5+ - 8.5', 231684, '100,000+ - 500,000'),
(50, 'Colossal', 'tt4680182', '21/04/2017', '2015+', 'movie', 'R', 'Action,Comedy,Sci-Fi', 0, '0 - 1', 110, '60+ - 120', 'English', 'Spain,Canada', 59, '50+ - 65', 6.7, '6.5+ - 7.5', 672, '500+ - 750'),
(51, 'Dunkirk', 'tt5013056', '21/07/2017', '2015+', 'movie', 'PG-13', 'Action,Drama,Thriller', 0, '0 - 1', 0, '0 - 2', 'English,French,German', 'Netherlands,UK,France,USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(52, 'Thor: Ragnarok', 'tt3501632', '03/11/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Fantasy', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(53, 'The Lost City of Z', 'tt1212428', '14/04/2017', '2015+', 'movie', 'PG-13', 'Action,Adventure,Biography', 0, '0 - 1', 141, '120+', 'English,Spanish,Portuguese,German', 'USA', 84, '75+ - 85', 7.3, '6.5+ - 7.5', 1245, '1,000+ - 2,500'),
(54, 'Gifted', 'tt4481414', '12/04/2017', '2015+', 'movie', 'PG-13', 'Drama', 0, '0 - 1', 101, '60+ - 120', 'English', 'USA', 60, '50+ - 65', 7.8, '7.5+ - 8.5', 1485, '1,000+ - 2,500'),
(55, 'The Secret Life of Pets', 'tt2709768', '08/07/2016', '2015+', 'movie', 'PG', 'Animation,Adventure,Comedy', 0, '0 - 1', 87, '60+ - 120', 'English', 'Japan,USA', 61, '50+ - 65', 6.6, '6.5+ - 7.5', 117910, '100,000+ - 500,000'),
(56, 'It', 'tt1396484', '08/09/2017', '2015+', 'movie', 'N/A', 'Drama,Horror', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(57, 'Rogue One', 'tt3748528', '16/12/2016', '2015+', 'movie', 'PG-13', 'Action,Adventure,Sci-Fi', 0, '0 - 1', 133, '120+', 'English', 'USA', 65, '50+ - 65', 7.9, '7.5+ - 8.5', 315407, '100,000+ - 500,000'),
(58, 'La La Land', 'tt3783958', '25/12/2016', '2015+', 'movie', 'PG-13', 'Comedy,Drama,Music', 0, '0 - 1', 128, '120+', 'English', 'USA,HongKong', 93, '85+ - 95', 8.3, '7.5+ - 8.5', 232825, '100,000+ - 500,000'),
(59, 'Avengers: Infinity War', 'tt4154756', '04/05/2018', '2015+', 'movie', 'N/A', 'Action,Adventure,Fantasy', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(61, 'The Boss Baby', 'tt3874544', '31/03/2017', '2015+', 'movie', 'PG', 'Animation,Comedy,Family', 0, '0 - 1', 97, '60+ - 120', 'English', 'USA', 50, '35+ - 50', 6.5, '5.0+ - 6.5', 10901, '10,000+ - 25,000'),
(66, 'X-Men: Apocalypse', 'tt3385516', '27/05/2016', '2015+', 'movie', 'PG-13', 'Action,Adventure,Sci-Fi', 0, '0 - 1', 144, '120+', 'English,Polish,German,Arabic,Egyptian(Ancient)', 'USA', 52, '50+ - 65', 7.1, '6.5+ - 7.5', 272645, '100,000+ - 500,000'),
(67, 'Spider-Man: Homecoming', 'tt2250912', '07/07/2017', '2015+', 'movie', 'N/A', 'Action,Adventure,Sci-Fi', 0, '0 - 1', 0, '0 - 2', 'English', 'USA', 0, '0 - 10', 0, '0.0 - 1.0', 0, '0 - 50'),
(69, 'Attack on Titan', 'tt2560140', '01/04/2013', '2010+ - 2015', 'series', 'TV-14', 'Animation,Action,Adventure', 2, '1+ - 3', 24, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 8.8, '8.5+ - 9.5', 76679, '50,000+ - 100,000'),
(72, 'One Punch Man', 'tt4508902', '01/10/2015', '2010+ - 2015', 'series', 'TV-PG', 'Animation,Action,Comedy', 1, '0 - 1', 24, '20+ - 30', 'Japanese,English', 'Japan', 0, '0 - 10', 9.1, '8.5+ - 9.5', 43720, '25,000+ - 50,000'),
(73, 'Death Note', 'tt0877057', '03/10/2006', '2005+ - 2010', 'series', 'TV-14', 'Animation,Crime,Drama', 1, '0 - 1', 24, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 9, '8.5+ - 9.5', 127332, '100,000+ - 500,000'),
(74, 'Dragon Ball Super', 'tt4644488', '05/07/2015', '2010+ - 2015', 'series', 'TV-PG', 'Animation,Action,Adventure', 1, '0 - 1', 24, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 8.2, '7.5+ - 8.5', 7573, '5,000+ - 10,000'),
(76, 'Naruto: ShippÃ»den', 'tt0988824', '28/10/2009', '2005+ - 2010', 'series', 'TV-14', 'Animation,Action,Adventure', 24, '10+ - 25', 24, '20+ - 30', 'Japanese', 'Japan', 0, '0 - 10', 8.5, '7.5+ - 8.5', 49032, '25,000+ - 50,000'),
(77, 'Howl\'s Moving Castle', 'tt0347149', '17/06/2005', '2000+ - 2005', 'movie', 'PG', 'Animation,Adventure,Family', 0, '0 - 1', 119, '60+ - 120', 'Japanese', 'Japan', 80, '75+ - 85', 8.2, '7.5+ - 8.5', 232453, '100,000+ - 500,000'),
(78, 'Princess Mononoke', 'tt0119698', '12/07/1997', '1990+ - 2000', 'movie', 'PG-13', 'Animation,Adventure,Fantasy', 0, '0 - 1', 134, '120+', 'Japanese', 'Japan', 76, '75+ - 85', 8.4, '7.5+ - 8.5', 241602, '100,000+ - 500,000'),
(79, 'My Neighbor Totoro', 'tt0096283', '16/04/1988', '1980+ - 1990', 'movie', 'G', 'Animation,Family,Fantasy', 0, '0 - 1', 86, '60+ - 120', 'Japanese', 'Japan', 0, '0 - 10', 8.2, '7.5+ - 8.5', 189943, '100,000+ - 500,000'),
(80, 'Dragon Ball Z', 'tt0214341', '13/09/1996', '1990+ - 2000', 'series', 'TV-PG', 'Animation,Action,Adventure', 15, '10+ - 25', 24, '20+ - 30', 'English', 'Japan,USA,Canada', 0, '0 - 10', 8.8, '8.5+ - 9.5', 85142, '50,000+ - 100,000'),
(81, 'Fairy Tail: The Movie - Dragon Cry', 'tt6548966', '06/05/2017', '2015+', 'movie', 'N/A', 'Animation,Action,Adventure', 0, '0 - 1', 0, '0 - 2', 'Japanese', 'Japan', 0, '0 - 10', 10, '9.5+ - 10.0', 29, '0 - 50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`movie_index`),
  ADD UNIQUE KEY `movie_index` (`movie_index`),
  ADD UNIQUE KEY `imdb_Ref_ID` (`imdb_Ref_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `movie_index` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
