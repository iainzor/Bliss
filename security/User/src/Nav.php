<?php
namespace User;

class Nav extends \Pages\Container
{
	public function __construct(User $user, \Request\Module $request) 
	{
		$this->add([
			[
				"title" => "Overview",
				"path" => "account",
				"element" => "user-account-section-overview"
			], [
				"title" => "Profile",
				"path" => "account/profile",
				"element" => "user-account-section-profile"
			], [
				"title" => "Settings",
				"path" => "account/settings",
				"element" => "user-account-section-settings"
			], [
				"is" => "divider"
			], [
				"title" => "Sign Out",
				"path" => "sign-out",
				"className" => "danger"
			]
		]);
		
		$section = $request->param("section");
		$parts = ["account"];
		
		if ($section) {
			$parts[] = $section;
		}
		
		$path = implode("/", $parts);
		
		foreach ($this as $page) {
			$page->activate($path);
		}
	}
}