set global event_scheduler = on;


create event archiverCampagnesExpirees
on schedule every 1 minute do 
begin
    update campagnes
    set status = 'terminee', dateModifier = now()
    where status = 'approuvee' and dateFin < now();

    update campagnes c
    join campagnesFinancieres cf on c.id = cf.id
    set c.status = 'terminee', c.dateModifier = now()
    where c.status = 'approuvee' and cf.objectifMontant > cf.montantCollecte;
end

drop event archiverCampagnesExpirees;


update campagnes c
    join campagnesParrainage cp on c.id = cp.id
    set c.status = 'terminee', c.dataModifier = now()

declare v_frequence varchar(50);

    select frequence campagnesParrainage

    if 






