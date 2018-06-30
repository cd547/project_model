create table pro_analysis
(
  id           int auto_increment
  comment '自增id'
    primary key,
  pro_num      varchar(32)  not null
  comment ' 项目编号',
  pro_problem  text         null
  comment ' 思考',
  pro_solve    text         null
  comment ' 分析',
  notes        varchar(256) null
  comment ' 备注',
  createTime   datetime     null
  comment ' 录入时间',
  createPeople varchar(16)  null
  comment ' 录入人',
  updateTime   datetime     null
  comment ' 修改时间',
  updatePeople varchar(16)  null
  comment ' 修改人'
)
  engine = MyISAM;


