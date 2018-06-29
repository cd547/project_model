-- auto-generated definition
create table pro_level1
(
  id                  int auto_increment
    primary key,
  Sys_id              int                 not null
  comment '一级项目明细序号',
  pro_num             varchar(32)         not null
  comment '项目编号',
  pro_startTime       date                null
  comment '项目开始时间',
  pro_endTime         date                null
  comment '项目结束时间',
  pro_name            varchar(64)         not null
  comment '项目名称',
  pro_content         text                null
  comment '项目内容',
  pro_address01       varchar(32)         null
  comment '项目归属',
  pro_address02       varchar(32)         null
  comment '项目实施地点',
  pro_serviceObj      varchar(64)         null
  comment '服务对象',
  pro_endMoney        decimal(13, 2)      null
  comment '最高限价',
  pro_reportMoney     decimal(13, 2)      not null
  comment '申报金额',
  pro_payMethod       varchar(128)        null
  comment '付款方式',
  pro_condition       varchar(128)        null
  comment '承接组织须具备的条件',
  pro_sub_content     text                null
  comment '递交项目文本',
  pro_manage_require  text                null
  comment '项目监管要求',
  pro_endCase_require text                null
  comment '项目结案要求',
  pro_dem_company     varchar(64)         null
  comment '项目需求方信息（需求单位）',
  pro_dem_contacts    varchar(16)         null
  comment '项目需求方信息（联系人）',
  pro_dem_mobile      varchar(16)         null
  comment '项目需求方信息（联系电话）',
  pro_agent_company   varchar(64)         null
  comment '项目代理方信息（代理单位）',
  pro_agent_contacts  varchar(16)         null
  comment '项目代理方信息（联系人）',
  pro_agent_mobile    varchar(16)         null
  comment '项目代理方信息（联系单位）',
  pro_type            varchar(32)         null
  comment '所属项目类型',
  pro_createTime      datetime            null
  comment '项目入录日期',
  pro_updateTime      datetime            null
  comment '项目修改日期',
  pro_enterPeople     varchar(16)         null
  comment '项目入录人',
  pro_updatePeople    varchar(16)         null
  comment '项目修改人',
  pro_status          tinyint default '0' null
  comment '项目状态 0:未启动
1:进行中
2:已完成'
);

create table pro_level2
(
  id               int auto_increment
  comment '自增id'
    primary key,
  Sys_id           int            not null
  comment '一级项目明细序号',
  second_id        int            null
  comment '二级项目明细序号',
  pro_num          varchar(32)    not null
  comment '项目编号',
  pro_endMoney     decimal(13, 2) null
  comment '最高限价',
  pro_serviceObj   varchar(64)    null
  comment '服务对象',
  pro_startTime    datetime       null
  comment '项目开始时间',
  pro_endTime      datetime       null
  comment '项目结束时间',
  pro_reportMoney  decimal(13, 2) null
  comment '申报金额',
  pro_content      text           null
  comment '项目内容',
  second_content   text           null
  comment '二级项目构成内容说明',
  second_money     decimal(13, 2) null
  comment '二级项目构成内容下的花费金额',
  pro_endateTime   datetime       null
  comment '项目入录日期',
  pro_updateTime   datetime       null
  comment '项目修改日期',
  pro_enterPeople  varchar(16)    null
  comment '项目入录人',
  pro_updatePeople varchar(16)    null
  comment '项目修改人'
);

create table pro_level3
(
  id               int auto_increment
  comment '自增id'
    primary key,
  Sys_id           int            not null
  comment '一级项目明细序号',
  second_id        int            null
  comment '二级项目明细序号',
  third_id         int            null
  comment '三级项目明细序号',
  third_content    varchar(128)   null
  comment '三级项目明细内容',
  third_money      decimal(13, 2) null
  comment '三级项目明细金额',
  third_basis      varchar(64)    null
  comment '测算依据',
  pro_endateTime   datetime       null
  comment '项目入录日期',
  pro_updateTime   datetime       null
  comment '项目修改日期',
  pro_enterPeople  varchar(16)    null
  comment '项目修改人',
  pro_updatePeople varchar(16)    null
  comment '项目修改人'
);

create table users
(
  id             int(10) auto_increment
  comment '用户id'
    primary key,
  username       varchar(20)            not null
  comment '用户名',
  showname       varchar(32)            not null
  comment '显示名',
  password       varchar(32)            null
  comment '密码',
  code           varchar(16)            null
  comment '工号',
  status         tinyint(1)             not null
  comment '用户状态，0锁定，1激活',
  role           varchar(20)            not null
  comment '用户角色',
  position       varchar(32)            null
  comment '职位',
  cellphone      varchar(32)            not null
  comment '手机号',
  wechart        varchar(32)            not null
  comment '微信号',
  email          varchar(255)           not null
  comment '电子邮件',
  msg_notify     tinyint(1) default '0' not null
  comment '短信提醒 0：关闭，1：开启',
  wechart_notify tinyint(1) default '0' not null
  comment '微信提醒 0：关闭，1：开启',
  email_notify   tinyint(1) default '0' not null
  comment '邮件提醒 0：关闭，1：开启',
  time_reg       int                    null
  comment '注册时间',
  time_last      int                    null
  comment '最后登录时间',
  department     varchar(32)            null
  comment '部门'
);


