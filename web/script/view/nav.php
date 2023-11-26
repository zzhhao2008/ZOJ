<nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top" style="background: #e3f2fd;;">
	<div class="container">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"><?= view::icon("list") ?></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
			<a class="navbar-brand" href="/"><img decoding="async" src="/icon.jpg" alt="Logo" style="width:30px;"> ZZHCode</a>
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link active" id="contanctnaver" aria-current="page" href="/contanct">交流</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="problemnaver" href="/problem">题目</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="contestnaver" href="/contest">比赛</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="practicenaver" href="/practice">练习</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="submissionsnaver" href="/submissions">提交记录</a>
				</li>
				<?php if (user::is_superuser()) : ?>
					<li class="nav-item">
						<a class="nav-link" id='user_manage' href="/user_manage">
							用户管理
						</a>
					</li>
				<?php endif; ?>
			</ul>

			<form class="d-flex" role="search" action="/search">
				<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="s">
				<button class="btn btn-outline-success" type="submit">Go</button>
			</form>
			<ul class="navbar-nav">
				<li class="nav-item"><a class="nav-link" href="/profile"><?= view::icon("person-circle") ?>
						<?= $GLOBALS['userprofile'] ? $GLOBALS['userprofile']['nick'] : "登录" ?></a></li>
			</ul>

		</div>
	</div>
</nav>