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
    prenom varchar(100) not null,
    email varchar(150) unique,
    password varchar(255) not null,
    imageProfile varchar(255),
    dateCreation timestamp default(now()),
    dateModifier timestamp,
    idRole int,
    constraint FK_role foreign key (idRole) references roles (id)
);

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

create table categories (
    id int primary key auto_increment,
    nom varchar(100) not null
);

create table organisations (
    id int primary key auto_increment,
    idadherent int not null,
    nom varchar(150) not null,
    identifiantfiscal varchar(50),
    adresse text,
    ribassociation varchar(24),
    recepisse varchar(255),
    pvelection varchar(255),
    statuts varchar(255),
    attestationrib varchar(255),
    cnipresidentrecto varchar(255),
    cnipresidentverso varchar(255),
    estverifie boolean default false,
    datecreation timestamp default current_timestamp,
    datemodifier timestamp on update current_timestamp,
    constraint fk_org_adherent foreign key (idadherent) references adherents (id)
);

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
    estVerifie boolean default false,
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp,
    constraint FK_adherent foreign key (id) references adherents (id)
);

create table ribs (
    id int primary key,
    rib varchar(50) not null,
    attestationRib varchar(255) not null,
    estVerifie boolean default false,
    dateCreation timestamp default current_timestamp,
    dateModifier timestamp,
    constraint FK_adherent foreign key (id) references adherents (id)
);

delimiter

create procedure login (
    p_email varchar(150)
)
begin
   declare v_count int;

    select count(*) into v_count
    from users 
    where email = p_email;

    if v_count = 0 then
       signal sqlstate '45000'
       set message_text = 'Email ou mot de passe incorrect';
    else
       select u.*, r.nom as role
       from users u
       join roles r on u.idRole = r.id
       where u.email = p_email;
    end if;   
end;

drop procedure login;

create procedure register (
    p_nom varchar(100),
    p_prenom varchar(100),
    p_email varchar(150),
    p_password varchar(250),
    p_sexe varchar(5),
    p_dateNaissance date,
    p_role varchar(50)
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

    select id into v_role
    from roles
    where nom = p_role;
    
    start transaction;
        insert into users (nom, prenom, email, password, idRole)
            values (p_nom, p_prenom, p_email, p_password, v_role);

        insert into adherents (id, sexe, dateNaissance)
            values (last_insert_id(), p_sexe, p_dateNaissance);
    commit;

end;

delimiter;

drop procedure register;

insert into roles (nom) values ('admin'), ('adherent');