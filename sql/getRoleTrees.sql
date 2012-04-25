USE iwoon;
DELIMITER $$
DROP PROCEDURE IF EXISTS getRoleTrees$$
CREATE PROCEDURE getRoleTrees(in roleid INT,in direction varchar(10),in dept_in tinyint,in drop_table_tmp tinyint)
BEGIN
	declare prid int DEFAULT NULL;
  declare dept_v tinyint DEFAULT 0;
  set dept_v=dept_in;
	set prid=roleid; #(select parent_role_id from prj_rbac_roles where role_id=roleid);
        CREATE TEMPORARY TABLE IF NOT EXISTS T (
            role_id int default 0,
            parent_role_id int default null,
            dept int default 0)engine=Memory;
    CASE direction
        WHEN 'parent' THEN 
            WHILE(prid IS NOT NULL) DO
            insert into T(select role_id,parent_role_id,(select dept_v) as dept from prj_rbac_roles where role_id=prid);
            set prid=(select parent_role_id from prj_rbac_roles where role_id=prid);
            set dept_v=dept_v+1;
            END WHILE;
        WHEN 'child' THEN
            BLOCK1:BEGIN 
                declare goto boolean default true;
                declare cur_child_roles cursor for select role_id from prj_rbac_roles where parent_role_id=prid;
                declare continue handler for not found set goto=false;
                open cur_child_roles;
                delete from T where role_id=prid;
                insert into T(select role_id,parent_role_id,(select dept_v) as dept from prj_rbac_roles where role_id=prid);
                set dept_v=dept_v+1;
                insert into T(select role_id,parent_role_id,(select dept_v) as dept from prj_rbac_roles where parent_role_id=prid);
                LOOP1:loop
                    fetch cur_child_roles into prid;
                    if goto then
                        #insert into T(select role_id,parent_role_id,(select dept_v) as dept from prj_rbac_roles where parent_role_id=prid);
                        #set max recursion
                        SET @@GLOBAL.max_sp_recursion_depth = 255;
                        SET @@session.max_sp_recursion_depth = 255;
                        call getRoleTrees(prid,'child',dept_v,false);
                    end if;
                    close cur_child_roles;
                    leave LOOP1;
                END loop LOOP1;
            END BLOCK1;
        END CASE;
select role_id,parent_role_id,dept from T order by dept ASC;
if(drop_table_tmp) then
    DROP TEMPORARY TABLE IF EXISTS T;
end if;
END$$