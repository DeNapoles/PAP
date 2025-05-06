CREATE TABLE IF NOT EXISTS `VariedadeFAQs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `texto` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserting the 4 components
INSERT INTO `VariedadeFAQs` (`nome`, `texto`, `link`, `imagem`) VALUES
('GIAE', 'Precisas de ajuda no GIAE?', 'FAQs-GIAE.php', 'img/blog/cat-widget1.jpg'),
('Moodle', 'Precisas de ajuda no Moodle?', 'FAQs-Moodle.php', 'img/blog/cat-widget2.jpg'),
('Computador escolar', 'Precisas de ajuda com o teu computador da escola?', 'FAQs-CompEscolar.php', 'img/blog/cat-widget3.jpg'),
('Acessórios do kit', 'Precisas de ajuda com algum acessório do kit digital?', 'FAQs-AcessoriosKit.php', 'img/blog/cat-widget3.jpg'); 