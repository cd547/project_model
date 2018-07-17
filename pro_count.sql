create table pro_count
(
  id          int auto_increment
    primary key,
  pro_year    int             not null,
  num         int default '0' not null
  comment '当年项目数',
  create_time datetime        null,
  constraint pro_count_year_uindex
  unique (pro_year)
)
  comment '项目计数表';


