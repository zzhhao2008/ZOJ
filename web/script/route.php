<?php
Router::any("/", "index");
Router::any("problem", "problem");
Router::any("judgequeue", "api/judgequeue");
Router::any("judgedetial", "api/judgedetial");
Router::any("judgeres", "api/judgeres");

Router::login("contest", "contest");
Router::login("contanct", "contanct/list");
Router::login("contancting", "contanct/show");
Router::login("ccontanct", "contanct/create");
Router::login("profile", "user/profile");
Router::login("practice", "practice");
Router::login("logup", "profile");
Router::login("change", "user/change");
Router::login("submission", "submission/look");
Router::login("submissions", "submission/list");
Router::login("contanctmanage", "contanct/manage");


Router::guest("contest", "user/login");
Router::guest("profile", "user/login");
Router::guest("contanct", "user/login");
Router::guest("contancting", "user/login");
Router::guest("practice", "user/login");
Router::guest("logup", "user/logup");
Router::guest("submisson", "user/login");
Router::guest("submission", "user/login");
Router::guest("submissions", "user/login");

Router::admin("user_manage", "admin/user/manage");
Router::admin("user_edit", "admin/user/edit");
Router::admin("user_cr_rm", "admin/user/crm");
Router::admin("problem_edit", "admin/problem/edit");
Router::admin("problem_cr_rm", "admin/problem/crm");
Router::admin("problem_edit_c", "admin/problem/editC");
Router::admin("problem_edit_d", "admin/problem/editData");
