<?php
class FailedreasonModel extends Model {
	protected $_validate         =         array(
			array('F_Reason','require','失败原因必须填写！'), //默认情况下用正则进行验证
	);
}