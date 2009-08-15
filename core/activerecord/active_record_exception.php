<?php

class ActiveRecordException extends Exception {
	const RecordNotFound    = 0;
	const AttributeNotFound = 1;
	const UnexpectedClass   = 2;
	const ObjectFrozen      = 3;
	const HasManyThroughCantAssociateNewRecords = 4;
	const MethodOrAssocationNotFound = 5;
}


?>