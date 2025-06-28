-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 01:05 PM
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
-- Database: `starbucks_hr`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `date`, `created_at`) VALUES
(1, 'Welcome New Employees', 'We are excited to welcome our new team members to the Starbucks family. Please join us for orientation on Monday.', '2025-01-15', '2025-01-15 08:00:00'),
(2, 'Holiday Schedule', 'Please note our updated holiday schedule for the upcoming season. All stores will be closed on major holidays.', '2025-01-10', '2025-01-10 08:00:00'),
(3, 'Training Workshop', 'Mandatory customer service training workshop scheduled for all employees next week. Please check your schedule.', '2025-01-05', '2025-01-05 08:00:00'),
(4, 'PABILISAN MANGULILA SA TAFT AVENUE', '\'Di mo ba ako lilisanin?\r\nHindi pa ba sapat pagpapahirap sa \'kin? (Damdamin ko)\r\nHindi na ba ma-mamamayapa?\r\nHindi na ba ma-mamamayapa?\r\nHindi na makalaya\r\nDinadalaw mo \'ko bawat gabi\r\nWala mang nakikita\r\nHaplos mo\'y ramdam pa rin sa dilim', '2025-06-25', '2025-06-25 03:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_email` varchar(255) NOT NULL,
  `applicant_phone` varchar(20) DEFAULT NULL,
  `status` enum('Pending','Reviewed','Shortlisted','Interview Scheduled','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `applicant_name`, `applicant_email`, `applicant_phone`, `status`, `applied_at`) VALUES
(1, 1, 'sophie', 'sophianicolepburgos@iskolarngbayan.pup.edu.ph', '02112322', 'Accepted', '2025-06-21 02:40:41'),
(2, 2, 'ALEX', 'xani@gmail.com', '02112322', 'Reviewed', '2025-06-21 03:00:41'),
(3, 4, 'Diluc', 'dawnwinery@gmail.com', '02112322', 'Accepted', '2025-06-21 03:06:40'),
(4, 3, 'Jogga Line', 'jogs@gmail.com', '098212345', 'Accepted', '2025-06-24 13:51:27'),
(5, 1, 'Rex Lapis', 'mora@gmail.com', '09847640985', 'Rejected', '2025-06-24 15:43:39'),
(6, 6, 'Princess Iniwan', 'siopauiii@gmail.com', '09345678911', 'Accepted', '2025-06-25 03:33:09'),
(7, 7, 'Paul Malone', 'congratulations@gmail.com', '09876543211', 'Interview Scheduled', '2025-06-25 04:37:02'),
(8, 7, 'Rodje Cyrus Magtanggol', 'rodje@gmail.com', '09654312870', 'Interview Scheduled', '2025-06-25 05:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `application_documents`
--

CREATE TABLE `application_documents` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application_documents`
--

INSERT INTO `application_documents` (`id`, `application_id`, `file_path`, `original_filename`, `file_type`) VALUES
(1, 1, '1-68561ba9cf5a5-sophie.pdf', 'Resume_Sophia.pdf', 'application/pdf'),
(2, 2, '2-6856205986ed1-Resume_Sophia (1).pdf', 'Resume_Sophia(1).pdf', 'application/pdf'),
(3, 3, '3-685621c011431-DOCUMENTATION.pdf', 'DOCUMENTATION.pdf', 'application/pdf'),
(4, 4, '4-685aad5fc53dd-insurance.pdf', 'insurance.pdf', 'application/pdf'),
(5, 5, '5-685ac7aba0caf-insurance.pdf', 'insurance.pdf', 'application/pdf'),
(6, 6, '6-685b6df536aa4-insurance.pdf', 'insurance.pdf', 'application/pdf'),
(7, 7, '7-685b7cee6545e-DIT 2-3 YEAR-END PARTY.pdf', 'DIT 2-3 YEAR-END PARTY.pdf', 'application/pdf'),
(8, 8, '8-685b880da41bf-IM-Case Study.pdf', 'IM-Case Study.pdf', 'application/pdf');

-- --------------------------------------------------------

--
-- Table structure for table `application_notes`
--

CREATE TABLE `application_notes` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `status` enum('Active','On Leave','Resigned','Terminated') NOT NULL DEFAULT 'Active',
  `archived` tinyint(1) DEFAULT 0,
  `salary` decimal(10,2) DEFAULT NULL,
  `tax_info` varchar(255) DEFAULT NULL,
  `deductions` decimal(10,2) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `unit_building` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `position`, `department`, `status`, `archived`, `salary`, `tax_info`, `deductions`, `hire_date`, `profile_pic`, `address`, `phone`, `birthday`, `gender`, `civil_status`, `mobile`, `unit_building`, `street`, `city`, `province`, `zipcode`, `profile_picture`, `created_at`) VALUES
(1, 'Taylor Swift', 'taylor.swift@starbucks.com', 'Barista', 'Store Operations', 'Terminated', 1, 25000.00, NULL, NULL, '2024-01-15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(2, 'Toti Marie', 'toti.marie@starbucks.com', 'Operations', 'Store Operations', 'Active', 1, 30000.00, NULL, NULL, '2023-06-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(3, 'Grow Garden', 'grow.garden@starbucks.com', 'Store Manager', 'Management', 'Active', 1, 45000.00, NULL, NULL, '2022-03-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(4, 'Diluc', 'dawnwinery@gmail.com', 'Customer Service Representative', 'Customer Service', 'Active', 0, 25000.00, NULL, NULL, '2025-06-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(5, 'Jogga Line', 'jogs@gmail.com', 'Store Manager', 'Management', 'Active', 0, 25000.00, NULL, NULL, '2025-06-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(6, 'Rex Lapis', 'mora@gmail.com', 'Barista', 'Store Operations', 'Active', 1, 25000.00, NULL, NULL, '2025-06-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24 15:20:50'),
(7, 'Toti Marie', 'totimarie@starbucks.com', 'Assistant Store Manager', 'Store Operations', 'Active', 0, 2000.00, NULL, NULL, '2025-06-15', NULL, NULL, NULL, '1989-01-02', 'Female', 'Married', '09562233769', 'Bagong Barangay Housing Project West Zamora St. Pandacan, Manila, 841', '841', 'Manila', 'Metro Manila', '1011', NULL, '2025-06-25 03:28:06'),
(8, 'sophie', 'sophianicolepburgos@iskolarngbayan.pup.edu.ph', 'Barista', 'Store Operations', 'Active', 0, 25000.00, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-25 03:29:43'),
(9, 'Princess Iniwan', 'siopauiii@gmail.com', 'Demon Hunter', 'Management', 'Active', 0, 25000.00, NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-25 03:34:03');

-- --------------------------------------------------------

--
-- Table structure for table `interview_schedules`
--

CREATE TABLE `interview_schedules` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `interviewer` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_schedules`
--

INSERT INTO `interview_schedules` (`id`, `application_id`, `interview_date`, `interview_time`, `location`, `interviewer`, `notes`, `status`, `created_at`) VALUES
(1, 7, '2025-06-30', '14:00:00', 'Google Meet', 'Ms. Clorinde', 'bawal talaga', 'Scheduled', '2025-06-25 04:55:01'),
(2, 8, '2025-06-27', '10:00:00', 'Itech', 'Jandel', 'Minumulto na \'ko ng damdamin ko', 'Scheduled', '2025-06-25 05:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `qualifications` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `pay` varchar(100) NOT NULL,
  `job_type` varchar(50) NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `qualifications`, `location`, `department`, `pay`, `job_type`, `deadline`, `status`, `created_at`) VALUES
(1, 'Barista', 'Join our team as a Barista and be part of creating the Starbucks Experience. You will be responsible for crafting beverages, connecting with customers, and maintaining store cleanliness. This role offers flexible scheduling and opportunities for growth within our organization.\r\n\r\nKey Responsibilities:\r\n• Prepare and serve beverages according to Starbucks standards\r\n• Maintain store cleanliness and organization\r\n• Provide excellent customer service\r\n• Work collaboratively with team members\r\n• Follow food safety and sanitation guidelines\r\n\r\nThis position offers flexible scheduling, competitive pay, and opportunities for advancement within our organization.', 'High school diploma, Customer service experience, Ability to work in a fast-paced environment, Team player, Flexible schedule availability', 'Downtown Store', 'Store Operations', '$15-18/hour', 'Part-time', '2024-12-31', 'Open', '2025-06-24 15:20:50'),
(2, 'Shift Supervisor', 'As a Shift Supervisor, you will lead store operations during your shift, ensuring excellent customer service and team performance. You will train and coach baristas, manage inventory, and maintain store standards. This position offers leadership development and career advancement opportunities.\r\n\r\nKey Responsibilities:\r\n• Lead store operations during assigned shifts\r\n• Train and coach team members\r\n• Manage inventory and ordering\r\n• Ensure store cleanliness and safety standards\r\n• Handle customer concerns and complaints\r\n• Support store manager in achieving business goals\r\n\r\nThis role provides leadership experience and opportunities for advancement to Store Manager positions.', 'Previous Starbucks experience preferred, Leadership experience, Strong communication skills, Problem-solving abilities, Availability for various shifts', 'Mall Location', 'Store Operations', '$18-22/hour', 'Full-time', '2024-12-31', 'Open', '2025-06-24 15:20:50'),
(3, 'Store Manager', 'Lead a team of passionate partners in creating the Starbucks Experience. As a Store Manager, you will oversee all aspects of store operations including team development, financial performance, customer satisfaction, and community engagement. This role offers competitive benefits and opportunities for regional advancement.\r\n\r\nKey Responsibilities:\r\n• Lead and develop a team of 15-25 partners\r\n• Manage store financial performance and budgets\r\n• Ensure exceptional customer experience\r\n• Oversee inventory management and ordering\r\n• Implement company policies and procedures\r\n• Build relationships with the local community\r\n• Drive sales and operational excellence\r\n\r\nThis position offers competitive salary, comprehensive benefits, and opportunities for regional advancement.', 'Bachelor\'s degree preferred, 3+ years retail management experience, Strong leadership and communication skills, Financial acumen, Customer-focused mindset', 'University Plaza', 'Management', '$45,000-55,000/year', 'Full-time', '2024-12-31', 'Open', '2025-06-24 15:20:50'),
(4, 'Customer Service Representative', 'Serve customers at our high-traffic airport location. You will provide exceptional service to travelers, handle cash transactions, and maintain a welcoming environment. This position requires flexibility with scheduling and the ability to work in a dynamic airport setting.\r\n\r\nKey Responsibilities:\r\n• Provide exceptional customer service to travelers\r\n• Handle cash and card transactions accurately\r\n• Maintain store cleanliness and organization\r\n• Work efficiently in a high-traffic environment\r\n• Follow airport security protocols\r\n• Collaborate with airport staff and security\r\n\r\nThis position offers competitive pay, flexible scheduling, and the opportunity to serve customers from around the world.', 'Customer service experience, Cash handling skills, Ability to work flexible hours, Airport security clearance required, Strong interpersonal skills', 'Airport Location', 'Customer Service', '$16-20/hour', 'Full-time', '2024-12-31', 'Open', '2025-06-24 15:20:50'),
(5, 'Assistant Store Manager', 'Helps Manage the store, trains staff, monitor sales and customer service.', 'Bachelor\'s Degree preferred, 2+ years of leadership experience, Strong team coordination skills', 'Anonas', 'Retail Operations', '25,000-35,000', 'Full-time', NULL, 'Open', '2025-06-24 16:46:35'),
(6, 'Demon Hunter', 'I don\'t think you\'re ready for thе takedown\r\nBreak you into pieces in the world of pain \'cause you\'re all the same\r\nYeah, it\'s a takedown\r\nA demon with no feelings don\'t deserve to live, it\'s so obvious', 'Good at singing, dancing, and rapping. Must be an all rounder. BUHAY SI JINU', 'Seoul', 'Management', '100,000-300,000', 'Full-time', NULL, 'Open', '2025-06-25 03:31:29'),
(7, 'Grow a Garden', 'It\'s raining tacos\r\nFrom out of the sky\r\nTacos\r\nNo need to ask why\r\nJust open your mouth and close your eyes\r\nIt\'s raining tacos\r\nIt\'s raining tacos\r\nOut in the street\r\nTacos\r\nAll you can eat\r\nLettuce and shells\r\nCheese and meat\r\nIt\'s raining tacos', 'Magaling magpalaki ng fruits, nag-aambag sa harvest ', 'Roblox', 'Human Resources', '1,000,000,000-10,000,000,000,000', 'Full-time', NULL, 'Open', '2025-06-25 04:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `employee_id`, `leave_type`, `status`, `start_date`, `end_date`, `reason`, `document_path`, `admin_notes`, `processed_at`, `created_at`) VALUES
(1, 7, 'Vacation', 'Approved', '2025-06-24', '2025-06-27', 'gusto ko na magbakasyonnnn', 'uploads/leave_documents/leave_7_1750822755.jpg', 'sige, magbakasyon ka na', '2025-06-25 03:40:46', '2025-06-25 03:39:15'),
(2, 9, 'Maternity', 'Approved', '2025-06-18', '2025-06-30', 'you\'re my soda pop', 'uploads/leave_documents/leave_9_1750823809.pdf', 'okayed\r\n', '2025-06-25 03:57:11', '2025-06-25 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `tax_info` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `pay_period` date NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `net_pay` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `employee_id`, `pay_period`, `basic_salary`, `allowances`, `deductions`, `tax`, `net_pay`, `created_at`) VALUES
(1, 7, '0000-00-00', 500.00, 100.00, 30.00, 150.00, 420.00, '2025-06-25 03:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation` text NOT NULL,
  `award` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'admin@starbucks.com', '$2y$10$eMJ30VgbDnBNXWbYDj7OeeNj75Ci91KRcz9vIk3wWBWPJ4xxvMIoW', 'admin'),
(2, 'dawnwinery@gmail.com', '$2y$10$eMJ30VgbDnBNXWbYDj7OeeNj75Ci91KRcz9vIk3wWBWPJ4xxvMIoW', 'employee'),
(3, 'jogs@gmail.com', '$2y$10$eMJ30VgbDnBNXWbYDj7OeeNj75Ci91KRcz9vIk3wWBWPJ4xxvMIoW', 'employee'),
(4, 'mora@gmail.com', '$2y$10$eMJ30VgbDnBNXWbYDj7OeeNj75Ci91KRcz9vIk3wWBWPJ4xxvMIoW', 'employee'),
(5, 'totimarie@starbucks.com', '$2y$10$62rDGtPE0OZOuCQLQg3s.OvDP3.gcJUeraYU.AJp33HmC6sJ4NVGi', 'employee'),
(7, 'sophianicolepburgos@iskolarngbayan.pup.edu.ph', '$2y$10$PRuiyL8.UJL5amrw3TY6Muk1f3vAh/rlYyCe6jcTsoAsGOEe9fgYS', 'employee'),
(8, 'siopauiii@gmail.com', '$2y$10$dx3Tge17L7KX0/.wRj9Irenjd/UoM4WSLSrtUvA7ovY56tD4Wo0OS', 'employee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `application_notes`
--
ALTER TABLE `application_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `application_notes`
--
ALTER TABLE `application_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD CONSTRAINT `application_documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `application_notes`
--
ALTER TABLE `application_notes`
  ADD CONSTRAINT `application_notes_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD CONSTRAINT `interview_schedules_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payslips`
--
ALTER TABLE `payslips`
  ADD CONSTRAINT `payslips_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance`
--
ALTER TABLE `performance`
  ADD CONSTRAINT `performance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
