<?php
Router::any("/", "index");
Router::any("problem", "problem");
Router::any("judgequeue", "api/judgequeue");
Router::any("judgedetial", "api/judgedetial");
Router::any("judgeres", "api/judgeres");
Router::any("print","api/printer");
Router::any("visituser","user/visit");
Router::any("api/speak", "api/goodspeak");
Router::any("clist", "api/contestlist");
Router::any("cjudge", "api/judgecontest");

Router::login("getcontest-p","api/getproblemincontest");
Router::login("contest", "contest/index");
Router::login("contestshowing", "contest/detial");
Router::login("contanct", "contanct/list");
Router::login("contancting", "contanct/show");
Router::login("ccontanct", "contanct/create");
Router::login("profile", "user/profile");
Router::login("practice", "practice/list");
Router::login("logup", "profile");
Router::login("change", "user/change");
Router::login("submission", "submission/look");
Router::login("submissions", "submission/list");
Router::login("contanctmanage", "contanct/manage");
Router::login("themeset","user/themeset");
Router::login("practiceshow","practice/ditial");
Router::login("teamwork","teamwork/console");
Router::login("team/manage","teamwork/manage");

Router::guest("practice", "user/login");
Router::guest("logup", "user/logup");


Router::admin("user_manage", "admin/user/manage");
Router::admin("user_edit", "admin/user/edit");
Router::admin("user_cr_rm", "admin/user/crm");
Router::admin("problem_edit", "admin/problem/edit");
Router::admin("problem_cr_rm", "admin/problem/crm");
Router::admin("problem_edit_c", "admin/problem/editC");
Router::admin("problem_edit_d", "admin/problem/editData");
Router::admin("problem_judge", "admin/problem/judge");
Router::admin("practice_editor", "admin/practice/edit");
Router::admin("practice_create_common", "admin/practice/create");