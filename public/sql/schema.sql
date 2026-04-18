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

create table donations (
    id int primary key auto_increment,
    idCampagne int not null,
    idAdherent int not null,
    montant decimal(12, 2) not null check (montant > 0),
    status enum('en_attente', 'complete', 'echoue') default 'complete',
    dateDon timestamp default current_timestamp,
    
    constraint fk_donation_campagne foreign key (idCampagne) references campagnes (id) on delete cascade,
    constraint fk_donation_adherent foreign key (idAdherent) references adherents (id) on delete cascade
);

drop table donations;

drop table organisations;

create table campagnes (
    id int primary key auto_increment,
    idAdherent int not null,
    idCategorie int not null,
    titre varchar(100) not null,
    description text not null,
    image varchar(255) not null,
    telephone varchar(50) not null,
    dateDebut date not null,
    dateFin date not null,
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
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp on update current_timestamp,
    dateSupprimer timestamp default null,
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
        idRole,
        estVerifieGmail
    )
values (
        'Benaissa',
        'Morad',
        'admin@tamghrabit.com',
        '$2y$10$z1FDIsUS8TMzhXva8upAM.dTvJ2hFK.sZjVLf0Wlr1VcJ11uB5Wpi',
        1,
        true
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
INSERT IGNORE INTO
    categories (id, nom)
VALUES (1, 'Santé'),
    (2, 'Éducation'),
    (3, 'Social'),
    (4, 'Nature'),
    (5, 'Orphelins'),
    (6, 'Urgence');

update campagnes set dateSupprimer = null;
SET FOREIGN_KEY_CHECKS = 0;

drop table if exists campagnesNature;
drop table if exists campagnesAssociation;
drop table if exists campagnesParrainage;
drop table if exists CampagnesArgent;
drop table if exists campagnesFinancieres;
drop table if exists donations;
drop table if exists campagnes;
drop table if exists organisations;

SET FOREIGN_KEY_CHECKS = 1;
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE campagnesNature;
TRUNCATE TABLE campagnesAssociation;
TRUNCATE TABLE campagnesParrainage;
TRUNCATE TABLE CampagnesArgent;
TRUNCATE TABLE campagnesFinancieres;
TRUNCATE TABLE campagnes;
TRUNCATE TABLE organisations;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO organisations (id, idAdherent, nom, identifiantFiscal, adresse, ribAssociation, status)
VALUES 
(1, 7, 'Association Tawassol', 'IF882299', 'Rabat, Agdal', '007123456789012345678911', 'approuvee'),
(2, 8, 'Fondation Al Khair', 'IF445566', 'Casablanca, Maarif', '007987654321098765432122', 'approuvee');

INSERT INTO campagnes (id, idAdherent, idCategorie, titre, description, image, telephone, dateDebut, dateFin, type, status)
VALUES 
(1, 7, 1, 'Urgence Chirurgie', 'Aide pour une opération urgente.', 'donation_1.jpg', '0661001122', '2026-04-01', '2026-06-01', 'argent', 'approuvee'),
(2, 8, 3, 'Paniers Ramadan', 'Distribution de nourriture.', 'donation_1.jpg', '0661334455', '2026-04-01', '2026-05-15', 'argent', 'approuvee'),
(3, 7, 5, 'Kafala Yassine', 'Parrainage mensuel pour orphelin.', 'donation_1.jpg', '0661001122', '2026-04-01', '2027-04-01', 'parrainage', 'approuvee'),
(4, 8, 5, 'Kafala Sarah', 'Soutien scolaire et médical.', 'donation_1.jpg', '0661334455', '2026-04-01', '2027-04-01', 'parrainage', 'approuvee'),
(5, 7, 2, 'Laptops pour Etudiants', 'Collecte de 10 ordinateurs.', 'donation_1.jpg', '0661001122', '2026-04-01', '2026-08-01', 'nature', 'approuvee'),
(6, 8, 3, 'Vêtements Hiver', 'Collecte de manteaux et couvertures.', 'donation_1.jpg', '0661334455', '2026-04-01', '2026-11-01', 'nature', 'approuvee'),
(7, 7, 1, 'Centre de Dialyse', 'Equipement par Association Tawassol.', 'donation_1.jpg', '0661001122', '2026-04-01', '2026-12-30', 'association', 'approuvee'),
(8, 8, 4, 'Reboisement Atlas', 'Projet Fondation Al Khair.', 'donation_1.jpg', '0661334455', '2026-04-01', '2026-10-30', 'association', 'approuvee');

INSERT INTO campagnesFinancieres (id, objectifMontant, montantCollecte)
VALUES 
(1, 50000.00, 12000.00),
(2, 30000.00, 5000.00),
(3, 2000.00, 400.00),
(4, 2000.00, 200.00),
(7, 200000.00, 45000.00),
(8, 150000.00, 10000.00);

INSERT INTO CampagnesArgent (id) VALUES (1), (2);

INSERT INTO campagnesParrainage (id, frequence) VALUES (3, 'mensuel'), (4, 'mensuel');

INSERT INTO campagnesAssociation (id, idOrganisation) VALUES (7, 1), (8, 2);

INSERT INTO campagnesNature (id, typeDon, nomArticle) 
VALUES (5, 'materiel', 'Ordinateurs Portables'), (6, 'materiel', 'Vêtements');