-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 09:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `action_type` varchar(20) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `user` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `action_type`, `table_name`, `record_id`, `old_data`, `new_data`, `user`, `timestamp`, `description`) VALUES
(1, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 2\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 2\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Active\"}', 'yuiiy', '2025-04-25 05:10:52', ''),
(2, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Active\"}', 'yuiiy', '2025-04-25 05:46:28', ''),
(3, 'update', 'inventory', 2, '{\"id\":2,\"property_number\":\"2020-1-08-05-070-03-0001-2017\",\"description\":\"radio transreciever edited\",\"model_number\":31242342,\"serial_number\":34223423,\"acquisition_date_cost\":\"dec 21 2020 325,000\",\"person_accountable\":\"erroljohnpardillo\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"erroljohnpardillo\"}', '{\"property_number\":\"2020-1-08-05-070-03-0001-2017\",\"description\":\"radio transreciever edited\",\"model_number\":\"31242342\",\"serial_number\":\"34223423\",\"acquisition_date_cost\":\"dec 21 2020 325,000\",\"person_accountable\":\"erroljohnpardillo\",\"status\":\"Active\"}', 'erroljohnpardillo', '2025-04-25 05:46:42', ''),
(4, 'update', 'inventory', 16, '{\"id\":16,\"property_number\":\"748932749899234\",\"description\":\"this should be number 4\",\"model_number\":0,\"serial_number\":0,\"acquisition_date_cost\":\"5000\",\"person_accountable\":\"NONE\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-08\"}', '{\"property_number\":\"748932749899234\",\"description\":\"this should be number 4\",\"model_number\":\"0\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"5000\",\"person_accountable\":\"NONE\",\"status\":\"Active\"}', 'NONE', '2025-04-25 05:54:37', ''),
(5, 'update', 'inventory', 20, '{\"id\":20,\"property_number\":\"hjafskdlfjkalsdjfkljfkalsdfjkl\",\"description\":\"test newly added data logss\",\"model_number\":0,\"serial_number\":234312,\"acquisition_date_cost\":\"6000\",\"person_accountable\":\"NONE\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"hjafskdlfjkalsdjfkljfkalsdfjkl\",\"description\":\"test newly added data logss\",\"model_number\":\"0\",\"serial_number\":\"234312\",\"acquisition_date_cost\":\"6000\",\"person_accountable\":\"NONE\",\"status\":\"Active\"}', 'NONE', '2025-04-25 05:57:22', ''),
(6, 'update', 'inventory', 21, '{\"id\":21,\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"edited\",\"model_number\":0,\"serial_number\":0,\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"noneof\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"edited\",\"model_number\":\"0\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"noneof\",\"status\":\"Active\"}', 'noneof', '2025-04-25 06:00:44', ''),
(7, 'update', 'inventory', 19, '{\"id\":19,\"property_number\":\"jfakldsjfklasdj\",\"description\":\"trial edited number 4\",\"model_number\":0,\"serial_number\":0,\"acquisition_date_cost\":\"324234\",\"person_accountable\":\"asdfasd\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"jfakldsjfklasdj\",\"description\":\"trial edited number 4\",\"model_number\":\"0\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"324234\",\"person_accountable\":\"asdfasd\",\"status\":\"Active\"}', 'asdfasd', '2025-04-25 06:03:50', ''),
(8, 'update', 'inventory', 4, '{\"id\":4,\"property_number\":\"2020-1-08-05-070-03-030-0001\",\"description\":\"printertyerui\",\"model_number\":2147483647,\"serial_number\":2147483647,\"acquisition_date_cost\":\"april 13 2016 55,000\",\"person_accountable\":\"erroljohnpardillo\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"erroljohnpardillo\"}', '{\"property_number\":\"2020-1-08-05-070-03-030-0001\",\"description\":\"printertyerui\",\"model_number\":\"2147483647\",\"serial_number\":\"2147483647\",\"acquisition_date_cost\":\"april 13 2016 55,000\",\"person_accountable\":\"erroljohnpardillo\",\"status\":\"Active\"}', 'erroljohnpardillo', '2025-04-25 06:05:10', ''),
(9, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Inactive\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiiy\",\"status\":\"Inactive\"}', 'yuiiy', '2025-04-25 06:08:57', ''),
(10, 'update', 'inventory', 11, '{\"id\":11,\"property_number\":\"fjasdklfjkjkljklfjaskl\",\"description\":\"dajfklsadjfklnothertrieaplasdf\",\"model_number\":2147483647,\"serial_number\":0,\"acquisition_date_cost\":\"jfadkslfjkalj\",\"person_accountable\":\"j20984930284\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-12\"}', '{\"property_number\":\"fjasdklfjkjkljklfjaskl\",\"description\":\"dajfklsadjfklnothertrieaplasdf\",\"model_number\":\"2147483647\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"jfadkslfjkalj\",\"person_accountable\":\"j20984930284\",\"status\":\"Active\"}', 'j20984930284', '2025-04-25 06:28:01', ''),
(11, 'update', 'inventory', 21, '{\"id\":21,\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"editedfasdfa\",\"model_number\":0,\"serial_number\":0,\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"noneof\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"editedfasdfa\",\"model_number\":\"0\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"noneof\",\"status\":\"Active\"}', 'noneof', '2025-04-25 06:28:42', ''),
(12, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"40000\",\"status\":\"Inactive\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"40000\",\"status\":\"Inactive\"}', '40000', '2025-04-25 06:53:10', ''),
(13, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"4040\",\"status\":\"Inactive\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"4040\",\"status\":\"Inactive\"}', '4040', '2025-04-25 06:53:51', ''),
(14, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":34567890,\"serial_number\":34,\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiyuiyuigyhuiyu\",\"status\":\"Retired\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"trial number 3\",\"model_number\":\"34567890\",\"serial_number\":\"34\",\"acquisition_date_cost\":\" 567fgyu\",\"person_accountable\":\"yuiyuiyuigyhuiyu\",\"status\":\"Retired\"}', 'yuiyuiyuigyhuiyu', '2025-04-25 06:54:04', ''),
(15, 'update', 'inventory', 21, '{\"id\":21,\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"editedfasdfa\",\"model_number\":2147483647,\"serial_number\":2147483647,\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"2025-04-25\",\"status\":\"Retired\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"2342341hfjaskldfhjkl2j3kl43\",\"description\":\"editedfasdfa\",\"model_number\":\"23674892347\",\"serial_number\":\"7489132748\",\"acquisition_date_cost\":\"58930489034\",\"person_accountable\":\"2025-04-25\",\"status\":\"Retired\"}', '2025-04-25', '2025-04-25 07:10:07', ''),
(16, 'update', 'inventory', 1, '{\"id\":1,\"property_number\":\"wertwertwert3423423\",\"description\":\"edited with styled edit.item.css\",\"model_number\":34567890,\"serial_number\":2147483647,\"acquisition_date_cost\":\"5000000\",\"person_accountable\":\"april 25, 2025\",\"status\":\"Retired\",\"signature_of_inventory_team_date\":\"yuiyuiyuigyhuiyu\"}', '{\"property_number\":\"wertwertwert3423423\",\"description\":\"edited with styled edit.item.css\",\"model_number\":\"34567890\",\"serial_number\":\"32390124789012384901001\",\"acquisition_date_cost\":\"5000000\",\"person_accountable\":\"april 25, 2025\",\"status\":\"Retired\"}', 'april 25, 2025', '2025-04-25 07:49:11', ''),
(17, 'update', 'inventory', 16, '{\"id\":16,\"property_number\":\"748932749899234\",\"description\":\"this should be number 4\",\"model_number\":345345,\"serial_number\":23452345,\"acquisition_date_cost\":\"5000\",\"person_accountable\":\"2025-04-08\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-08\"}', '{\"property_number\":\"748932749899234\",\"description\":\"this should be number 4\",\"model_number\":\"0345345\",\"serial_number\":\"23452345\",\"acquisition_date_cost\":\"5000\",\"person_accountable\":\"2025-04-08\",\"status\":\"Active\"}', '2025-04-08', '2025-04-25 07:56:21', ''),
(18, 'update', 'inventory', 12, '{\"id\":12,\"property_number\":\"asdfhasjkdfhjk\",\"description\":\"trial of the end \",\"model_number\":23452345,\"serial_number\":2345,\"acquisition_date_cost\":\"234524352\",\"person_accountable\":\"2025-04-16\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-16\"}', '{\"property_number\":\"asdfhasjkdfhjk\",\"description\":\"trial of the end \",\"model_number\":\"23452345\",\"serial_number\":\"2345\",\"acquisition_date_cost\":\"234524352\",\"person_accountable\":\"2025-04-16\",\"status\":\"Active\"}', '2025-04-16', '2025-04-25 07:56:31', ''),
(19, 'update', 'inventory', 11, '{\"id\":11,\"property_number\":\"fjasdklfjkjkljklfjaskl\",\"description\":\"dajfklsadjfklnothertrieaplasdf\",\"model_number\":2147483647,\"serial_number\":2345234,\"acquisition_date_cost\":\"jfadkslfjkalj\",\"person_accountable\":\"2025-04-12\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-12\"}', '{\"property_number\":\"fjasdklfjkjkljklfjaskl\",\"description\":\"dajfklsadjfklnothertrieaplasdf\",\"model_number\":\"2147483647\",\"serial_number\":\"2345234\",\"acquisition_date_cost\":\"jfadkslfjkalj\",\"person_accountable\":\"2025-04-12\",\"status\":\"Active\"}', '2025-04-12', '2025-04-25 07:56:36', ''),
(20, 'update', 'inventory', 10, '{\"id\":10,\"property_number\":\"2020-09-12312\",\"description\":\"printer laser jet pro mfp m281fdw\",\"model_number\":234324,\"serial_number\":2147483647,\"acquisition_date_cost\":\"63000\",\"person_accountable\":\"2025-04-23\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-23\"}', '{\"property_number\":\"2020-09-12312\",\"description\":\"printer laser jet pro mfp m281fdw\",\"model_number\":\"234324\",\"serial_number\":\"24352345234\",\"acquisition_date_cost\":\"63000\",\"person_accountable\":\"2025-04-23\",\"status\":\"Active\"}', '2025-04-23', '2025-04-25 07:56:41', ''),
(21, 'update', 'inventory', 13, '{\"id\":13,\"property_number\":\"2020-05-10605030-061-2017\",\"description\":\"pc unit\",\"model_number\":12839012,\"serial_number\":2147483647,\"acquisition_date_cost\":\"45,000\",\"person_accountable\":\"2016-05-16\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2016-05-16\"}', '{\"property_number\":\"2020-05-10605030-061-2017\",\"description\":\"pc unit\",\"model_number\":\"12839012\",\"serial_number\":\"23452345234523\",\"acquisition_date_cost\":\"45,000\",\"person_accountable\":\"2016-05-16\",\"status\":\"Active\"}', '2016-05-16', '2025-04-25 07:56:46', ''),
(22, 'update', 'inventory', 18, '{\"id\":18,\"property_number\":\"HOME TEST \",\"description\":\"test for the home \",\"model_number\":23452345,\"serial_number\":23452345,\"acquisition_date_cost\":\"23452345\",\"person_accountable\":\"2025-04-25\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"HOME TEST \",\"description\":\"test for the home \",\"model_number\":\"23452345\",\"serial_number\":\"23452345\",\"acquisition_date_cost\":\"23452345\",\"person_accountable\":\"2025-04-25\",\"status\":\"Active\"}', '2025-04-25', '2025-04-25 07:56:53', ''),
(23, 'update', 'inventory', 19, '{\"id\":19,\"property_number\":\"jfakldsjfklasdj\",\"description\":\"trial edited number 4\",\"model_number\":2345234,\"serial_number\":23452345,\"acquisition_date_cost\":\"324234\",\"person_accountable\":\"2025-04-25\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-25\"}', '{\"property_number\":\"jfakldsjfklasdj\",\"description\":\"trial edited number 4\",\"model_number\":\"2345234\",\"serial_number\":\"23452345\",\"acquisition_date_cost\":\"324234\",\"person_accountable\":\"2025-04-25\",\"status\":\"Active\"}', '2025-04-25', '2025-04-25 07:56:59', ''),
(24, 'update', 'inventory', 25, '{\"id\":25,\"property_number\":\"random property number\",\"description\":\"ennd of the trial\",\"model_number\":2147483647,\"serial_number\":789086169,\"acquisition_date_cost\":\"29399ph\",\"person_accountable\":\"0023-02-04\",\"status\":\"Inactive\",\"signature_of_inventory_team_date\":\"0023-02-04\"}', '{\"property_number\":\"random property number\",\"description\":\"ennd of the trial\",\"model_number\":\"2390482903\",\"serial_number\":\"789086169\",\"acquisition_date_cost\":\"29399ph\",\"person_accountable\":\"0023-02-04\",\"status\":\"Inactive\"}', '0023-02-04', '2025-04-25 08:01:14', ''),
(25, 'update', 'inventory', 26, '{\"id\":26,\"property_number\":\"create2ndweekEDITED \",\"description\":\"2ndweekinventorybackend\",\"model_number\":920934820,\"serial_number\":0,\"acquisition_date_cost\":\"1000\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-28\"}', '{\"property_number\":\"create2ndweekEDITED \",\"description\":\"2ndweekinventorybackend\",\"model_number\":\"920934820\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"1000\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\"}', '2025-04-28', '2025-04-28 05:50:50', ''),
(26, 'update', 'inventory', 26, '{\"id\":26,\"property_number\":\"create2ndweekEDITED \",\"description\":\"2ndweekinventorybackend\",\"model_number\":920934820,\"serial_number\":0,\"acquisition_date_cost\":\"1000\",\"person_accountable\":\"2ND WEEK\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-28\"}', '{\"property_number\":\"create2ndweekEDITED \",\"description\":\"2ndweekinventorybackend\",\"model_number\":\"920934820\",\"serial_number\":\"0\",\"acquisition_date_cost\":\"1000\",\"person_accountable\":\"2ND WEEK\",\"status\":\"Active\"}', '2ND WEEK', '2025-04-28 05:51:35', ''),
(27, 'create', 'inventory', 28, NULL, NULL, '123', '2025-04-28 06:47:19', 'Created new inventory item: third'),
(28, 'update', 'inventory', 28, '{\"id\":28,\"property_number\":\"3rd edited\",\"description\":\"third\",\"model_number\":123,\"serial_number\":123,\"acquisition_date_cost\":\"123\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-28\"}', '{\"property_number\":\"3rd edited\",\"description\":\"third\",\"model_number\":\"123\",\"serial_number\":\"123\",\"acquisition_date_cost\":\"123\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\"}', '2025-04-28', '2025-04-28 06:48:03', ''),
(29, 'update', 'inventory', 28, '{\"id\":28,\"property_number\":\"3rd  4th time edited\",\"description\":\"third\",\"model_number\":123,\"serial_number\":123,\"acquisition_date_cost\":\"123\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-28\"}', '{\"property_number\":\"3rd  4th time edited\",\"description\":\"third\",\"model_number\":\"123\",\"serial_number\":\"123\",\"acquisition_date_cost\":\"123\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\"}', '2025-04-28', '2025-04-28 06:57:15', ''),
(30, 'create', 'inventory', 29, NULL, NULL, '1234', '2025-04-28 06:59:10', 'Created new inventory item: FOURTH'),
(31, 'update', 'inventory', 29, '{\"id\":29,\"property_number\":\"4TH TIME EDITED\",\"description\":\"FOURTH\",\"model_number\":12324,\"serial_number\":1234,\"acquisition_date_cost\":\"1234\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\",\"signature_of_inventory_team_date\":\"2025-04-28\"}', '{\"property_number\":\"4TH TIME EDITED\",\"description\":\"FOURTH\",\"model_number\":\"12324\",\"serial_number\":\"1234\",\"acquisition_date_cost\":\"1234\",\"person_accountable\":\"2025-04-28\",\"status\":\"Active\"}', '2025-04-28', '2025-04-28 07:04:29', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
