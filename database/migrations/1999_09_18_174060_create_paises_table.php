<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CountryType;

//Tabla de paises
return new class extends Migration
{
    public function up()
    {
        Schema::create('paises', function (Blueprint $table){
            $table->id();
            $table->string('descripcion', 100);
            $table->string('indicativo', 25);
            $table->foreignId('next_question_id')
                  ->nullable()
                  ->default(2) // Establece el valor por defecto aquí
                  ->constrained('questions')
                  ->nullOnDelete();
            $table->timestamps();
        });
        /*
        INSERT INTO `paises` (`id`, `descripcion`, `indicativo`, `created_at`, `updated_at`) VALUES 
            (1, 'Colombia', '57', '2022-10-17 22:44:22', '2024-05-28 12:43:58'),
            (2, 'Afganistán', '93', NULL, NULL),
            (3, 'Albania', '355', NULL, NULL),
            (4, 'Argelia', '213', NULL, NULL),
            (5, 'Samoa Americana', '1684', NULL, NULL),
            (6, 'Andorra', '376', NULL, NULL),
            (7, 'Angola', '244', NULL, NULL),
            (8, 'Anguila', '1264', NULL, NULL),
            (9, 'Antigua y Barbuda', '1268', NULL, NULL),
            (10, 'Argentina', '54', NULL, NULL),
            (11, 'Armenia', '374', NULL, NULL),
            (12, 'Aruba', '297', NULL, NULL),
            (13, 'Australia', '61', NULL, NULL),
            (14, 'Territorio australiano', '672', NULL, NULL),
            (15, 'Austria', '43', NULL, NULL),
            (16, 'Azerbaiyán', '994', NULL, NULL),
            (17, 'Bahamas', '1242', NULL, NULL),
            (18, 'Bahrain', '973', NULL, NULL),
            (19, 'Bangladesh', '880', NULL, NULL),
            (20, 'Barbados', '1246', NULL, NULL),
            (21, 'Belarus', '375', NULL, NULL),
            (22, 'Bélgica', '32', NULL, NULL),
            (23, 'Belice', '501', NULL, NULL),
            (24, 'Benín', '229', NULL, NULL),
            (25, 'Bermuda', '1441', NULL, NULL),
            (26, 'Bután', '975', NULL, NULL),
            (27, 'Bolivia', '591', NULL, NULL),
            (28, 'Bosnia/Herzegovina', '387', NULL, NULL),
            (29, 'Botsuana', '267', NULL, NULL),
            (30, 'Brasil', '55', NULL, NULL),
            (31, 'Islas Vírgenes Británicas', '1284', NULL, NULL),
            (32, 'Brunéi', '673', NULL, NULL),
            (33, 'Bulgaria', '359', NULL, NULL),
            (34, 'Burkina Faso', '226', NULL, NULL),
            (35, 'Burundi', '257', NULL, NULL),
            (36, 'Camboya', '855', NULL, NULL),
            (37, 'Camerún', '237', NULL, NULL),
            (38, 'Islas de Cabo Verde', '238', NULL, NULL),
            (39, 'Islas Caimán', '1345', NULL, NULL),
            (40, 'República Centroafricana', '236', NULL, NULL),
            (41, 'Chad', '235', NULL, NULL),
            (42, 'Chile', '56', NULL, NULL),
            (43, 'China', '86', NULL, NULL),
            (44, 'Comoras', '269', NULL, NULL),
            (45, 'Congo (DROC), República Democrática del', '243', NULL, NULL),
            (46, 'Congo (ROC), República de', '242', NULL, NULL),
            (47, 'Islas Cook', '682', NULL, NULL),
            (48, 'Costa Rica', '506', NULL, NULL),
            (49, 'Croacia', '385', NULL, NULL),
            (50, 'Cuba', '53', NULL, NULL),
            (51, 'Chipre', '357', NULL, NULL),
            (52, 'República Checa', '420', NULL, NULL),
            (53, 'Dinamarca', '45', NULL, NULL),
            (54, 'Diego García', '246', NULL, NULL),
            (55, 'Yibuti', '253', NULL, NULL),
            (56, 'Dominica', '1767', NULL, NULL),
            (57, 'República Dominicana', '1', NULL, NULL),
            (58, 'Ecuador', '593', NULL, NULL),
            (59, 'Egipto', '20', NULL, NULL),
            (60, 'El Salvador', '503', NULL, NULL),
            (61, 'Guinea Ecuatorial', '240', NULL, NULL),
            (62, 'Eritrea', '291', NULL, NULL),
            (63, 'Estonia', '372', NULL, NULL),
            (64, 'Etiopía', '251', NULL, NULL),
            (65, 'Islas Malvinas', '500', NULL, NULL),
            (66, 'Islas Feroe', '298', NULL, NULL),
            (67, 'Fiyi', '679', NULL, NULL),
            (68, 'Finlandia', '358', NULL, NULL),
            (69, 'Francia', '33', NULL, NULL),
            (70, 'Guyana Francesa', '594', NULL, NULL),
            (71, 'Gabón', '241', NULL, NULL),
            (72, 'Gambia', '220', NULL, NULL),
            (73, 'Georgia', '995', NULL, NULL),
            (74, 'Alemania', '49', NULL, NULL),
            (75, 'Ghana', '233', NULL, NULL),
            (76, 'Gibraltar', '350', NULL, NULL),
            (77, 'Grecia', '30', NULL, NULL),
            (78, 'Groenlandia', '299', NULL, NULL),
            (79, 'Granada', '1473', NULL, NULL),
            (80, 'Guadalupe (Antillas Francesas)', '590', NULL, NULL),
            (81, 'Guatemala', '502', NULL, NULL),
            (82, 'Guernsey', '44', NULL, NULL),
            (83, 'Guinea', '224', NULL, NULL),
            (84, 'Guinea-Bisáu', '245', NULL, NULL),
            (85, 'Guyana', '592', NULL, NULL),
            (86, 'Haití', '509', NULL, NULL),
            (87, 'Honduras', '504', NULL, NULL),
            (88, 'Hong Kong', '852', NULL, NULL),
            (89, 'Hungría', '36', NULL, NULL),
            (90, 'Islandia', '354', NULL, NULL),
            (91, 'India', '91', NULL, NULL),
            (92, 'Indonesia', '62', NULL, NULL),
            (93, 'Irán', '98', NULL, NULL),
            (94, 'Iraq', '964', NULL, NULL),
            (95, 'Irlanda', '353', NULL, NULL),
            (96, 'Isla de Man', '44', NULL, NULL),
            (97, 'Israel', '972', NULL, NULL),
            (98, 'Italia', '39', NULL, NULL),
            (99, 'Costa de Marfil', '225', NULL, NULL),
            (100, 'Jamaica', '1876', NULL, NULL),
            (101, 'Japón', '81', NULL, NULL),
            (102, 'Jersey', '44', NULL, NULL),
            (103, 'Jordania', '962', NULL, NULL),
            (104, 'Kazajistán', '7', NULL, NULL),
            (105, 'Kenia', '254', NULL, NULL),
            (106, 'Kiribati', '686', NULL, NULL),
            (107, 'Corea (Norte)', '850', NULL, NULL),
            (108, 'Corea (Sur)', '82', NULL, NULL),
            (109, 'Kuwait', '965', NULL, NULL),
            (110, 'Kirguizistán', '996', NULL, NULL),
            (111, 'Laos', '856', NULL, NULL),
            (112, 'Letonia', '371', NULL, NULL),
            (113, 'Líbano', '961', NULL, NULL),
            (114, 'Lesoto', '266', NULL, NULL),
            (115, 'Liberia', '231', NULL, NULL),
            (116, 'Libia', '218', NULL, NULL),
            (117, 'Liechtenstein', '423', NULL, NULL),
            (118, 'Lituania', '370', NULL, NULL),
            (119, 'Luxemburgo', '352', NULL, NULL),
            (120, 'Macau', '853', NULL, NULL),
            (121, 'Macedonia', '389', NULL, NULL),
            (122, 'Madagascar', '261', NULL, NULL),
            (123, 'Malaui', '265', NULL, NULL),
            (124, 'Malasia', '60', NULL, NULL),
            (125, 'Maldivas', '960', NULL, NULL),
            (126, 'Malí', '223', NULL, NULL),
            (127, 'Malta', '356', NULL, NULL),
            (128, 'Islas Marshall', '692', NULL, NULL),
            (129, 'Martinica', '596', NULL, NULL),
            (130, 'Mauritania', '222', NULL, NULL),
            (131, 'Islas Mauricio', '230', NULL, NULL),
            (132, 'México', '52', NULL, NULL),
            (133, 'Micronesia', '691', NULL, NULL),
            (134, 'Moldova', '373', NULL, NULL),
            (135, 'Mónaco', '377', NULL, NULL),
            (136, 'Mongolia', '976', NULL, NULL),
            (137, 'Montenegro', '382', NULL, NULL),
            (138, 'Montserrat', '1664', NULL, NULL),
            (139, 'Marruecos', '212', NULL, NULL),
            (140, 'Mozambique', '258', NULL, NULL),
            (141, 'Myanmar (Birmania)', '95', NULL, NULL),
            (142, 'Namibia', '264', NULL, NULL),
            (143, 'Nauru', '674', NULL, NULL),
            (144, 'Nepal', '977', NULL, NULL),
            (145, 'Holanda', '31', NULL, NULL),
            (146, 'Antillas Neerlandesas (Bonaire, Curacao, Saba, St. Eustis)', '599', NULL, NULL),
            (147, 'Nueva Caledonia', '687', NULL, NULL),
            (148, 'Nueva Zelanda', '64', NULL, NULL),
            (149, 'Nicaragua', '505', NULL, NULL),
            (150, 'Níger', '227', NULL, NULL),
            (151, 'Nigeria', '234', NULL, NULL),
            (152, 'Islas Marianas del Norte', '1670', NULL, NULL),
            (153, 'Noruega', '47', NULL, NULL),
            (154, 'Omán', '968', NULL, NULL),
            (155, 'Pakistán', '92', NULL, NULL),
            (156, 'Palaos', '680', NULL, NULL),
            (157, 'Autoridad Palestina', '970', NULL, NULL),
            (158, 'Panamá', '507', NULL, NULL),
            (159, 'Papúa Nueva Guinea', '675', NULL, NULL),
            (160, 'Paraguay', '595', NULL, NULL),
            (161, 'Perú', '51', NULL, NULL),
            (162, 'Filipinas', '63', NULL, NULL),
            (163, 'Polonia', '48', NULL, NULL),
            (164, 'Portugal', '351', NULL, NULL),
            (165, 'Qatar', '974', NULL, NULL),
            (166, 'Reunión', '262', NULL, NULL),
            (167, 'Rumania', '40', NULL, NULL),
            (168, 'Rusia', '7', NULL, NULL),
            (169, 'Ruanda', '250', NULL, NULL),
            (170, 'Saipán (Islas Marianas del Norte)', '1670', NULL, NULL),
            (171, 'Samoa', '685', NULL, NULL),
            (172, 'San Marino', '378', NULL, NULL),
            (173, 'Santo Tomé/Príncipe', '239', NULL, NULL),
            (174, 'Arabia Saudita', '966', NULL, NULL),
            (175, 'Senegal', '221', NULL, NULL),
            (176, 'Serbia', '381', NULL, NULL),
            (177, 'Seychelles', '248', NULL, NULL),
            (178, 'Sierra Leona', '232', NULL, NULL),
            (179, 'Singapur', '65', NULL, NULL),
            (180, 'Isla de San Martín', '1721', NULL, NULL),
            (181, 'Eslovaquia', '421', NULL, NULL),
            (182, 'Eslovenia', '386', NULL, NULL),
            (183, 'Islas Salomón', '677', NULL, NULL),
            (184, 'Sudáfrica', '27', NULL, NULL),
            (185, 'Sudán Meridional', '211', NULL, NULL),
            (186, 'España', '34', NULL, NULL),
            (187, 'Sri Lanka', '94', NULL, NULL),
            (188, 'San Pedro/Miquelón', '508', NULL, NULL),
            (189, 'San Cristóbal/Nieves', '1869', NULL, NULL),
            (190, 'Santa Lucía', '1758', NULL, NULL),
            (191, 'San Vicente/Granadinas', '1784', NULL, NULL),
            (192, 'Sudán', '249', NULL, NULL),
            (193, 'Suriname', '597', NULL, NULL),
            (194, 'Suazilandia', '268', NULL, NULL),
            (195, 'Suecia', '46', NULL, NULL),
            (196, 'Suiza', '41', NULL, NULL),
            (197, 'Siria', '963', NULL, NULL),
            (198, 'Taiwán', '886', NULL, NULL),
            (199, 'Tayikistán', '992', NULL, NULL),
            (200, 'Tanzania', '255', NULL, NULL),
            (201, 'Tailandia', '66', NULL, NULL),
            (202, 'Togo', '228', NULL, NULL),
            (203, 'Tokelau', '690', NULL, NULL),
            (204, 'Tonga', '676', NULL, NULL),
            (205, 'Trinidad y Tobago', '1868', NULL, NULL),
            (206, 'Túnez', '216', NULL, NULL),
            (207, 'Turquía', '90', NULL, NULL),
            (208, 'Turkmenistán', '993', NULL, NULL),
            (209, 'Islas Turcas y Caicos', '1649', NULL, NULL),
            (210, 'Tuvalu', '688', NULL, NULL),
            (211, 'Uganda', '256', NULL, NULL),
            (212, 'Ucrania', '380', NULL, NULL),
            (213, 'Emiratos Árabes Unidos', '971', NULL, NULL),
            (214, 'Reino Unido', '44', NULL, NULL),
            (215, 'Uruguay', '598', NULL, NULL),
            (216, 'Uzbekistán', '998', NULL, NULL),
            (217, 'Vanuatu', '678', NULL, NULL),
            (218, 'Venezuela', '58', NULL, NULL),
            (219, 'Vietnam', '84', NULL, NULL),
            (220, 'Yemen', '967', NULL, NULL),
            (221, 'Zambia', '260', NULL, NULL),
            (222, 'Zimbabue', '263', NULL, NULL),
            (223, 'Estados Unidos', '1', NULL, NULL),
            (224, 'Otro', '905', NULL, NULL);
        */
    }

    public function down()
    {
        Schema::dropIfExists('paises');
    }
};
