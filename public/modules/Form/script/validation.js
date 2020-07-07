function matchThisCondition(condition, value){
	switch(condition){
		case 'password':
			return (value.length > 5);
		break;
		case 'email':
			return /^[^@^\s^\n^\t]+@[^@^\s^\n^\t]+\.[a-zA-Z]{2,5}$/.test(value);
		break;
	}
	return true;
}