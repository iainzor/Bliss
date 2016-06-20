<?php
namespace User\Form;

use Bliss\Component,
	User\User,
	User\Db\UsersTable,
	Request\Module as Request;

class ChangePasswordForm extends Component
{
	const FIELD_CURRENT_PASSWORD = "currentPassword";
	const FIELD_NEW_PASSWORD = "newPassword";
	const FIELD_CONFIRM_PASSWORD = "confirmPassword";
	
	/**
	 * @var User
	 */
	private $user;
	
	/**
	 * @var array
	 */
	protected $errors = [];
	
	/**
	 * Constructor
	 * 
	 * @param \User\User $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * Check if the fields in the form are valid
	 * 
	 * @param \Request\Module $request
	 * @return boolean
	 */
	public function isValid(Request $request)
	{
		$valid = true;
		$hasher = $this->user->passwordHasher();
		$current = $request->param(self::FIELD_CURRENT_PASSWORD);
		$new = $request->param(self::FIELD_NEW_PASSWORD);
		$confirm = $request->param(self::FIELD_CONFIRM_PASSWORD);
		
		if (!empty($current)) {
			if (!$hasher->matches($current, $this->user->password())) {
				$valid = false;
				$this->errors[self::FIELD_CURRENT_PASSWORD] = "Invalid password";
			}
		} else {
			$valid = false;
			$this->errors[self::FIELD_CURRENT_PASSWORD] = "Field cannot be empty";
		}
		
		if (!empty($new)) {
			if ($new !== $confirm) {
				$valid = false;
				$this->errors[self::FIELD_CONFIRM_PASSWORD] = "Confirmation does not match new password";
			}
		} else {
			$valid = false;
			$this->errors[self::FIELD_NEW_PASSWORD] = "Field cannot be empty";
		}
		
		if ($valid) {
			$this->user->password(
				$hasher->hash($new)
			);
		}
		
		return $valid;
	}
	
	public function execute()
	{
		$users = new UsersTable();
		$query = $users->update();
		$query->values([
			"password" => $this->user->password()
		]);
		$query->where([
			"id" => $this->user->id()
		]);
		$query->limit(1);
		$query->execute();
	}
}