create table pro_level1
(
  id                  int auto_increment
    primary key,
  Sys_id              int                           not null
  comment ' 一级项目明细序号',
  pro_num             varchar(32)                   not null
  comment ' 项目编号',
  pro_startTime       date                          null
  comment ' 项目开始时间',
  pro_endTime         date                          null
  comment ' 项目结束时间',
  pro_name            varchar(64)                   not null
  comment ' 项目名称',
  pro_content         text                          null
  comment ' 项目内容',
  pro_address01       varchar(32)                   null
  comment ' 项目归属',
  pro_address02       varchar(32)                   null
  comment ' 项目实施地点',
  pro_serviceObj      varchar(64)                   null
  comment ' 服务对象',
  pro_endMoney        decimal(13, 2) default '0.00' null
  comment ' 最高限价',
  pro_reportMoney     decimal(13, 2) default '0.00' not null
  comment ' 申报金额',
  pro_payMethod       varchar(128)                  null
  comment ' 付款方式',
  pro_condition       varchar(128)                  null
  comment ' 承接组织须具备的条件',
  pro_sub_content     text                          null
  comment ' 递交项目文本',
  pro_manage_require  text                          null
  comment ' 项目监管要求',
  pro_endCase_require text                          null
  comment ' 项目结案要求',
  pro_dem_company     varchar(64)                   null
  comment ' 项目需求方信息（需求单位）',
  pro_dem_contacts    varchar(16)                   null
  comment ' 项目需求方信息（联系人）',
  pro_dem_mobile      varchar(16)                   null
  comment ' 项目需求方信息（联系电话）',
  pro_agent_company   varchar(64)                   null
  comment ' 项目代理方信息（代理单位）',
  pro_agent_contacts  varchar(16)                   null
  comment ' 项目代理方信息（联系人）',
  pro_agent_mobile    varchar(16)                   null
  comment ' 项目代理方信息（联系单位）',
  pro_type            varchar(32)                   null
  comment ' 所属项目类型',
  pro_createTime      datetime                      null
  comment ' 项目入录日期',
  pro_updateTime      datetime                      null
  comment ' 项目修改日期',
  pro_enterPeople     varchar(16)                   null
  comment ' 项目入录人',
  pro_updatePeople    varchar(16)                   null
  comment ' 项目修改人',
  pro_status          tinyint default '0'           null
  comment '项目状态 0:未启动
  1:进行中
  2:已完成'
);


