CREATE TABLE project.pro_level1
(
    id int(11) PRIMARY KEY NOT NULL COMMENT '自增id' AUTO_INCREMENT,
    Sys_id int(11) NOT NULL COMMENT '一级项目明细序号',
    pro_num varchar(32) NOT NULL COMMENT '项目编号',
    pro_name varchar(64) NOT NULL COMMENT '项目名称',
    pro_address01 varchar(32) COMMENT '项目归属',
    pro_address02 varchar(32) COMMENT '项目实施地点',
    pro_payMethod varchar(128) COMMENT '付款方式',
    pro_condition varchar(128) COMMENT '承接组织须具备的条件',
    pro_sub_content text COMMENT '递交项目文本',
    pro_manage_require text COMMENT '项目监管要求',
    pro_endCase_require text COMMENT '项目结案要求',
    pro_dem_company varchar(64) COMMENT '项目需求方信息（需求单位）',
    pro_dem_contacts varchar(16) COMMENT '项目需求方信息（联系人）',
    pro_dem_mobile varchar(16) COMMENT '项目需求方信息（联系电话）',
    pro_agent_company varchar(64) COMMENT '项目代理方信息（代理单位）',
    pro_agent_contacts varchar(16) COMMENT '项目代理方信息（联系人）',
    pro_agent_mobile varchar(16) COMMENT '项目代理方信息（联系单位）',
    pro_type varchar(32) COMMENT '所属项目类型',
    pro_endateTime datetime COMMENT '项目入录日期',
    pro_updateTime datetime COMMENT '项目修改日期',
    pro_enterPeople varchar(16) COMMENT '项目入录人',
    pro_updatePeople varchar(16) COMMENT '项目修改人'
);
CREATE TABLE project.pro_level2
(
    id int(11) PRIMARY KEY NOT NULL COMMENT '自增id' AUTO_INCREMENT,
    Sys_id int(11) NOT NULL COMMENT '一级项目明细序号',
    second_id int(11) COMMENT '二级项目明细序号',
    pro_num varchar(32) NOT NULL COMMENT '项目编号',
    pro_endMoney decimal(13,2) COMMENT '最高限价',
    pro_serviceObj varchar(64) COMMENT '服务对象',
    pro_startTime datetime COMMENT '项目开始时间',
    pro_endTime datetime COMMENT '项目结束时间',
    pro_reportMoney decimal(13,2) COMMENT '申报金额',
    pro_content text COMMENT '项目内容',
    second_content text COMMENT '二级项目构成内容说明',
    second_money decimal(13,2) COMMENT '二级项目构成内容下的花费金额',
    pro_endateTime datetime COMMENT '项目入录日期',
    pro_updateTime datetime COMMENT '项目修改日期',
    pro_enterPeople varchar(16) COMMENT '项目入录人',
    pro_updatePeople varchar(16) COMMENT '项目修改人'
);
CREATE TABLE project.pro_level3
(
    id int(11) PRIMARY KEY NOT NULL COMMENT '自增id' AUTO_INCREMENT,
    Sys_id int(11) NOT NULL COMMENT '一级项目明细序号',
    second_id int(11) COMMENT '二级项目明细序号',
    third_id int(11) COMMENT '三级项目明细序号',
    third_content varchar(128) COMMENT '三级项目明细内容',
    third_money decimal(13,2) COMMENT '三级项目明细金额',
    third_basis varchar(64) COMMENT '测算依据',
    pro_endateTime datetime COMMENT '项目入录日期',
    pro_updateTime datetime COMMENT '项目修改日期',
    pro_enterPeople varchar(16) COMMENT '项目修改人',
    pro_updatePeople varchar(16) COMMENT '项目修改人'
);
CREATE TABLE project.users
(
    id int(10) PRIMARY KEY NOT NULL COMMENT '用户id' AUTO_INCREMENT,
    username varchar(20) NOT NULL COMMENT '用户名',
    showname varchar(32) NOT NULL COMMENT '显示名',
    password varchar(32) COMMENT '密码',
    code varchar(16) COMMENT '工号',
    status tinyint(1) NOT NULL COMMENT '用户状态，0锁定，1激活',
    role varchar(20) NOT NULL COMMENT '用户角色',
    position varchar(32) COMMENT '职位',
    cellphone varchar(32) NOT NULL COMMENT '手机号',
    wechart varchar(32) NOT NULL COMMENT '微信号',
    email varchar(255) NOT NULL COMMENT '电子邮件',
    msg_notify tinyint(1) DEFAULT '0' NOT NULL COMMENT '短信提醒 0：关闭，1：开启',
    wechart_notify tinyint(1) DEFAULT '0' NOT NULL COMMENT '微信提醒 0：关闭，1：开启',
    email_notify tinyint(1) DEFAULT '0' NOT NULL COMMENT '邮件提醒 0：关闭，1：开启',
    time_reg int(11) COMMENT '注册时间',
    time_last int(11) COMMENT '最后登录时间',
    department varchar(32) COMMENT '部门'
);
INSERT INTO project.users (id, username, showname, password, code, status, role, position, cellphone, wechart, email, msg_notify, wechart_notify, email_notify, time_reg, time_last, department) VALUES (1, 'admin', '管理员', 'c4ca4238a0b923820dcc509a6f75849b', null, 1, 'user', null, '', '', '', 0, 0, 0, 1529821599, 1529821616, null);