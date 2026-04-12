-- Active: 1767604599808@@127.0.0.1@3306@tamghrabit
create database Tamghrabit;

use Tamghrabit;

create table roles (
    id int primary key auto_increment,
    nom varchar(50) unique
);

create table users (
    id int primary key auto_increment,
    nom varchar(100) not null,
    prenom varchar(100) null,
    email varchar(150) unique,
    password varchar(255) not null,
    idGoogle varchar(255),
    tokenVerification varchar(255),
    estVerifieGmail boolean default false,
    imageProfile varchar(255),
    dateCreation timestamp default(now()),
    dateModifier timestamp on update current_timestamp,
    idRole int,
    constraint FK_role foreign key (idRole) references roles (id)
);

drop table users;
drop table adherents;

create table adherents (
    id int primary key,
    sexe varchar(10) not null,
    dateNaissance date,
    adresse varchar(255),
    ville varchar(100),
    telephoneCode varchar(5),
    telephone varchar(20),
    pays varchar(50),
    estVerifie boolean default false,
    constraint FK_user foreign key (id) references users (id),
    constraint CK_sexe check (sexe in ('homme', 'femme'))
);

create table conversations (
    id int primary key auto_increment,
    datecreation timestamp default(now())
);

drop table conversations;

create table conversationUsers (
    id int primary key auto_increment,
    idConversation int not null,
    idUser int not null,
    estSupprime boolean default 0,
    dateSupprimer timestamp null,
    constraint fk_User_conv foreign key (idConversation) references conversations (id) on delete cascade,
    constraint fk_user foreign key (idUser) references users (id) on delete cascade
);

create table messages (
    id int primary key auto_increment,
    idConversation int not null,
    idExpediteur int not null,
    contenu text not null,
    dateCreation timestamp default current_timestamp,
    constraint fk_message_conv foreign key (idConversation) references conversationUsers (id) on delete cascade,
    constraint fk_message_user foreign key (idExpediteur) references users (id)
);

create table categories (
    id int primary key auto_increment,
    nom varchar(100) not null
);

create table organisations (
    id int primary key auto_increment,
    idAdherent int not null,
    nom varchar(150) not null,
    identifiantFiscal varchar(50),
    adresse text,
    ribAssociation varchar(24),
    recepisse varchar(255),
    pvElection varchar(255),
    statuts varchar(255),
    attestationRib varchar(255),
    cniPresidentRecto varchar(255),
    cniPresidentVerso varchar(255),
    status enum(
        'en_attente',
        'approuvee',
        'refuse'
    ) default 'en_attente',
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp on update current_timestamp,
    constraint fk_org_adherent foreign key (idAdherent) references adherents (id)
);

drop table organisations;

create table campagnes (
    id int primary key auto_increment,
    idAdherent int not null,
    idCategorie int not null,
    titre varchar(100) not null,
    description text not null,
    image varchar(255) not null,
    telephone varchar(50) not null,
    datedebut date not null,
    datefin date not null,
    justificatif varchar(255),
    type enum(
        'parrainage',
        'nature',
        'argent',
        'association'
    ),
    status enum(
        'en_attente',
        'approuvee',
        'rejetee',
        'terminee'
    ) default 'en_attente',
    datecreation timestamp default current_timestamp,
    datemodifier timestamp on update current_timestamp,
    datesupprimer timestamp default null,
    constraint fk_campagne_adherent foreign key (idAdherent) references adherents (id),
    constraint fk_campagne_categorie foreign key (idCategorie) references categories (id)
);

create table campagnesFinancieres (
    id int primary key,
    objectifMontant decimal(12, 2) not null check (objectifMontant > 0),
    montantCollecte decimal(12, 2) default 0.00,
    constraint fk_fin_campagne foreign key (id) references campagnes (id)
);

create table CampagnesArgent (
    id int primary key,
    constraint fk_argent_fin foreign key (id) references campagnesFinancieres (id)
);

create table campagnesParrainage (
    id int primary key,
    frequence enum('mensuel', 'annuel') not null,
    constraint fk_parrainage_fin foreign key (id) references campagnesFinancieres (id)
);

create table campagnesAssociation (
    id int primary key,
    idOrganisation int not null,
    constraint fk_assoc_fin foreign key (id) references campagnesFinancieres (id),
    constraint fk_assoc_org foreign key (idOrganisation) references organisations (id)
);

create table campagnesNature (
    id int primary key,
    typeDon enum('materiel', 'expertise'),
    nomArticle varchar(255),
    constraint fk_nature_campagne foreign key (id) references campagnes (id)
);

create table identifiers (
    id int primary key,
    cniRecto varchar(255),
    cniVerso varchar(255),
    passport varchar(255),
    status enum(
        'en_attente',
        'approuvee',
        'refuse'
    ) default 'en_attente',
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp,
    constraint FK_adherent foreign key (id) references adherents (id)
);

create table ribs (
    id int primary key,
    rib varchar(50) not null,
    attestationRib varchar(255) not null,
    status enum(
        'en_attente',
        'approuvee',
        'refuse'
    ) default 'en_attente',
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp,
    constraint FK_adherent foreign key (id) references adherents (id)
);

drop table ribs;

drop table identifiers;

insert into
    users (
        nom,
        prenom,
        email,
        password,
        idRole
    )
values (
        'Benaissa',
        'Morad',
        'admin@tamghrabit.com',
        '$2y$10$fJfTE2isWREd2bYnCQGVcOCNXXUH4yyLoDCk2qShGf3lNCAG68VKa',
        1
    );

delimiter

drop procedure if exists login;

create procedure login (
    p_email varchar(150)
)
begin
    declare v_exists int;
    declare v_verified int;

    select count(*), max(estVerifieGmail) into v_exists, v_verified 
    from users 
    where email = p_email;

    if v_exists = 0 then
        signal sqlstate '45000' set message_text = 'Email ou mot de passe incorrect';
    elseif v_verified = 0 then
        signal sqlstate '45000' set message_text = 'Veuillez vérifier votre boîte email pour activer votre compte';
    else
        select u.*, r.nom as role
        from users u
        join roles r on u.idRole = r.id
        where u.email = p_email;
    end if;   
end;
drop procedure if exists register;

create procedure register (
    p_nom varchar(100),
    p_prenom varchar(100),
    p_email varchar(150),
    p_password varchar(250),
    p_sexe varchar(10),
    p_dateNaissance date,
    p_role varchar(50),
    p_tokenVerification varchar(255)
) 
begin
    declare v_role int default 0;
    
    declare exit handler for 1062
    begin
        rollback;
        signal sqlstate '45000'
        set message_text = 'Email déja utilisé';
    end;

    declare exit handler for sqlexception
    begin
        rollback;
        signal sqlstate '45000'
        set message_text = 'Erreur serveur';
    end;

    select id into v_role from roles where nom = p_role;
    
    start transaction;
        insert into users (nom, prenom, email, password, idRole, tokenVerification, estVerifieGmail)
            values (p_nom, p_prenom, p_email, p_password, v_role, p_tokenVerification, false);

        insert into adherents (id, sexe, dateNaissance)
            values (last_insert_id(), p_sexe, p_dateNaissance);
    commit;
end;
delimiter;

drop procedure register;

insert into roles (nom) values ('admin'), ('adherent');

-- 1. أولا: تنظيف الجداول القديمة (باش ما يوقعش تكرار للإدج أيدي)
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE campagnesNature;

TRUNCATE TABLE campagnesAssociation;

TRUNCATE TABLE campagnesParrainage;

TRUNCATE TABLE CampagnesArgent;

TRUNCATE TABLE campagnesFinancieres;

TRUNCATE TABLE campagnes;

SET FOREIGN_KEY_CHECKS = 1;

-- 2. تعمير الأصناف (إذا كانت خاوية)
INSERT IGNORE INTO
    categories (id, nom)
VALUES (1, 'Santé'),
    (2, 'Éducation'),
    (3, 'Social'),
    (4, 'Nature'),
    (5, 'Orphelins'),
    (6, 'Urgence');

-- 3. حملات من نوع ARGENT (10 سطور)
INSERT INTO
    campagnes (
        id,
        idAdherent,
        idCategorie,
        titre,
        description,
        image,
        telephone,
        datedebut,
        datefin,
        type,
        status
    )
VALUES (
        1,
        1,
        1,
        'Soutien Urgence Médicale',
        'Aide pour les soins intensifs.',
        'img_1_1774988780.png',
        '0661000001',
        '2026-04-01',
        '2026-06-01',
        'argent',
        'approuvee'
    ),
    (
        2,
        1,
        1,
        'Opération Coeur Ouvert',
        'Collecte pour un enfant de 5 ans.',
        'img_1_1774988780.png',
        '0661000002',
        '2026-04-01',
        '2026-07-01',
        'argent',
        'approuvee'
    ),
    (
        3,
        1,
        2,
        'Rénovation Salle de Classe',
        'Achat de tables et tableaux.',
        'img_1_1774988780.png',
        '0661000003',
        '2026-04-01',
        '2026-08-01',
        'argent',
        'approuvee'
    ),
    (
        4,
        1,
        3,
        'Paniers Solidaires',
        'Aide alimentaire mensuelle.',
        'img_1_1774988780.png',
        '0661000004',
        '2026-04-01',
        '2026-05-01',
        'argent',
        'approuvee'
    ),
    (
        5,
        1,
        6,
        'Aide Sinistrés Al Haouz',
        'Reconstruction des foyers.',
        'img_1_1774988780.png',
        '0661000005',
        '2026-04-01',
        '2026-12-01',
        'argent',
        'approuvee'
    ),
    (
        6,
        1,
        4,
        'Forage Puits Atlas',
        'Eau potable pour le village.',
        'img_1_1774988780.png',
        '0661000006',
        '2026-04-01',
        '2026-09-01',
        'argent',
        'approuvee'
    ),
    (
        7,
        1,
        2,
        'Bourses d\'études Supérieures',
        'Soutien aux étudiants Master.',
        'img_1_1774988780.png',
        '0661000007',
        '2026-04-01',
        '2026-10-01',
        'argent',
        'approuvee'
    ),
    (
        8,
        1,
        5,
        'Espace Jeux Orphelinat',
        'Aménagement parc de jeux.',
        'img_1_1774988780.png',
        '0661000008',
        '2026-04-01',
        '2026-06-01',
        'argent',
        'approuvee'
    ),
    (
        9,
        1,
        3,
        'Projet Micro-Couture',
        'Achat machines à coudre.',
        'img_1_1774988780.png',
        '0661000009',
        '2026-04-01',
        '2026-08-01',
        'argent',
        'approuvee'
    ),
    (
        10,
        1,
        1,
        'Soins Dialyse',
        'Prise en charge des séances.',
        'img_1_1774988780.png',
        '0661000010',
        '2026-04-01',
        '2026-05-01',
        'argent',
        'approuvee'
    );

INSERT INTO
    campagnesFinancieres (
        id,
        objectifMontant,
        montantCollecte
    )
SELECT id, 20000, 5000
FROM campagnes
WHERE
    type = 'argent';

INSERT INTO
    CampagnesArgent (id)
SELECT id
FROM campagnes
WHERE
    type = 'argent';

-- 4. حملات من نوع PARRAINAGE (10 سطور)
INSERT INTO
    campagnes (
        id,
        idAdherent,
        idCategorie,
        titre,
        description,
        image,
        telephone,
        datedebut,
        datefin,
        type,
        status
    )
VALUES (
        11,
        1,
        5,
        'Kafala Petit Yassine',
        'Soutien mensuel complet.',
        'img_1_1774988780.png',
        '0662000001',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        12,
        1,
        5,
        'Parrainage Amina',
        'Frais de scolarité.',
        'img_1_1774988780.png',
        '0662000002',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        13,
        1,
        2,
        'Soutien Etudiant Ingénierie',
        'Bourse de vie mensuelle.',
        'img_1_1774988780.png',
        '0662000003',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        14,
        1,
        5,
        'Parrainage Orphelin Omar',
        'Aide santé et nutrition.',
        'img_1_1774988780.png',
        '0662000004',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        15,
        1,
        3,
        'Soutien Famille Précaire',
        'Loyer et subsistance.',
        'img_1_1774988780.png',
        '0662000005',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        16,
        1,
        5,
        'Kafala Bébé Sara',
        'Lait et soins médicaux.',
        'img_1_1774988780.png',
        '0662000006',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        17,
        1,
        2,
        'Parrainage Transport Scolaire',
        'Abonnement bus pour 1 an.',
        'img_1_1774988780.png',
        '0662000007',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        18,
        1,
        5,
        'Soutien Jeune Apprenti',
        'Frais de formation pro.',
        'img_1_1774988780.png',
        '0662000008',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        19,
        1,
        5,
        'Parrainage Médical Permanent',
        'Traitement maladie chronique.',
        'img_1_1774988780.png',
        '0662000009',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    ),
    (
        20,
        1,
        2,
        'Soutien Sportif Espoir',
        'Equipement et entraînement.',
        'img_1_1774988780.png',
        '0662000010',
        '2026-04-01',
        '2027-04-01',
        'parrainage',
        'approuvee'
    );

INSERT INTO
    campagnesFinancieres (
        id,
        objectifMontant,
        montantCollecte
    )
SELECT id, 1200, 300
FROM campagnes
WHERE
    type = 'parrainage';

INSERT INTO
    campagnesParrainage (id, frequence)
SELECT id, 'mensuel'
FROM campagnes
WHERE
    type = 'parrainage';

-- 5. حملات من نوع NATURE (10 سطور)
INSERT INTO
    campagnes (
        id,
        idAdherent,
        idCategorie,
        titre,
        description,
        image,
        telephone,
        datedebut,
        datefin,
        type,
        status
    )
VALUES (
        21,
        1,
        2,
        'Collecte Laptops Étudiants',
        'Besoin de 20 PC portables.',
        'img_1_1774988780.png',
        '0663000001',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        22,
        1,
        3,
        'Don de Couvertures',
        'Pour l\'hiver en montagne.',
        'img_1_1774988780.png',
        '0663000002',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        23,
        1,
        1,
        'Fauteuils Roulants',
        'Besoin de 10 unités.',
        'img_1_1774988780.png',
        '0663000003',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        24,
        1,
        2,
        'Livres Bibliothèque Rurale',
        'Livres niveau primaire.',
        'img_1_1774988780.png',
        '0663000004',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        25,
        1,
        3,
        'Vêtements Neufs Aïd',
        'Pour 100 orphelins.',
        'img_1_1774988780.png',
        '0663000005',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        26,
        1,
        4,
        'Plants d\'Arbres Fruitiers',
        'Reboisement solidaire.',
        'img_1_1774988780.png',
        '0663000006',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        27,
        1,
        2,
        'Tablettes Éducatives',
        'Pour cours de soutien.',
        'img_1_1774988780.png',
        '0663000007',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        28,
        1,
        1,
        'Matériel Pansement',
        'Don pour petit dispensaire.',
        'img_1_1774988780.png',
        '0663000008',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        29,
        1,
        3,
        'Vélos Scolaires',
        'Transport pour filles rurales.',
        'img_1_1774988780.png',
        '0663000009',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    ),
    (
        30,
        1,
        3,
        'Kits Hygiène Femmes',
        'Distribution en zone rurale.',
        'img_1_1774988780.png',
        '0663000010',
        '2026-04-01',
        '2026-12-01',
        'nature',
        'approuvee'
    );

INSERT INTO
    campagnesNature (id, typeDon, nomArticle)
SELECT id, 'materiel', 'Equipement'
FROM campagnes
WHERE
    type = 'nature';

-- 6. حملات من نوع ASSOCIATION (10 سطور)
INSERT INTO
    campagnes (
        id,
        idAdherent,
        idCategorie,
        titre,
        description,
        image,
        telephone,
        datedebut,
        datefin,
        type,
        status
    )
VALUES (
        31,
        1,
        1,
        'Équipement Centre Dialyse',
        'Projet de la Fondation Nour.',
        'img_1_1774988780.png',
        '0664000001',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        32,
        1,
        2,
        'Digital School Project',
        'Équipement multimédia complet.',
        'img_1_1774988780.png',
        '0664000002',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        33,
        1,
        4,
        'Green Morocco 2026',
        'Nettoyage et reboisement.',
        'img_1_1774988780.png',
        '0664000003',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        34,
        1,
        3,
        'Maison de l\'Espoir',
        'Construction foyer étudiant.',
        'img_1_1774988780.png',
        '0664000004',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        35,
        1,
        1,
        'Bus de Don de Sang',
        'Unité mobile pour la ville.',
        'img_1_1774988780.png',
        '0664000005',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        36,
        1,
        3,
        'Cantine Solidaire',
        'Repas chauds pour sans-abris.',
        'img_1_1774988780.png',
        '0664000006',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        37,
        1,
        2,
        'Laboratoire Scientifique',
        'Matériel pour lycée public.',
        'img_1_1774988780.png',
        '0664000007',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        38,
        1,
        1,
        'Prothèses pour Tous',
        'Collecte pour handicapés.',
        'img_1_1774988780.png',
        '0664000008',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        39,
        1,
        3,
        'Centre de Formation Femmes',
        'Apprentissage et autonomie.',
        'img_1_1774988780.png',
        '0664000009',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    ),
    (
        40,
        1,
        1,
        'Hôpital de Jour Provincial',
        'Achat scanner médical.',
        'img_1_1774988780.png',
        '0664000010',
        '2026-04-01',
        '2026-12-30',
        'associationassociation',
        'approuvee'
    );

INSERT INTO
    campagnesFinancieres (
        id,
        objectifMontant,
        montantCollecte
    )
SELECT id, 100000, 25000
FROM campagnes
WHERE
    type = 'associationassociation';

INSERT INTO
    campagnesAssociation (id, idOrganisation)
SELECT id, 1
FROM campagnes
WHERE
    type = 'associationassociation';

update campagnes set image = 'donation_1.jpg';