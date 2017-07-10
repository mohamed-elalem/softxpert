const SUCCESS = 0;
const FAILED = 1;
const EXIST = 2;
const INVALID_CREDINTIALS = 3;
const ANONYMOUS = 4;
const AUTHENTICATED = 5;
const ALLOWED = 6;

const MESSAGES = {};

MESSAGES[SUCCESS] = "You've been registered successfully";
MESSAGES[FAILED] = "Server error please try again later";
MESSAGES[EXIST] = "You're already registered.\nplease login to continue using our services";
MESSAGES[INVALID_CREDINTIALS] = "Credintials provided are not correct.";