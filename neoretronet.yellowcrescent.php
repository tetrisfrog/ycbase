<?php

namespace neoretronet\yellowcrescent;

class ycBaseDex {
	protected $_yc_uuid;
	protected $_yc_depth;
	protected $_yc_root;
	protected $_yc_parent;
	protected $_yc_xopt;
	protected $_yc_children;
	protected $_yc_assert_last;

	protected $_yc_metadex;

	protected $_yc_meta;
	
	// Constructors & Destructors ///////////////////////////////////////////////////////
	public function __construct(&$pclx=NULL,$xopt=false) {
		if(!is_object($pclx) && !is_callable($pclx) && $pclx === NULL) {
			$_yc_parent = $this;
			$_yc_root = $this;
			$_yc_uuid = 0;
			$_yc_depth = 0;
		} else {
			$this->_yc_gen_uuid();
			$this->$_yc_parent = $pclx;
			$this->$_yc_root = $pclx->_yc_get_root();
			$this->$_yc_depth = $pclx->_yc_get_depth() + 1;
			$pclx->ycRegisterChild($this,$this->$_yc_uuid);
		}
		$this->$_yc_children = Array();		
		$this->$_yc_metadex = Array();
		$this->$_yc_xopt = $xopt;
	}

	final public function meta($lprop) {
		$xlx = explode(':', $lprop);
		$iterator = NULL;

		if(is_array($xlx) && (count($xlx) > 1)) {
			foreach($xlx as $arr_lval) {
				if($iterator === NULL) {
					if(isset($this->$_yc_meta[$arr_lval])) {
						$iterator = $this->$_yc_meta[$arr_lval];
					} else {
						// assert fail
						return NULL;
					}
				} else {
					if(isset($iterator[$arr_lval])) {
						$iterator = $iterator[$arr_lval];
					} else {
						// assert fail
						return NULL;
					}
				}
			}
		} else {
			if(isset($this->$_yc_meta[$arr_lval])) {
				$iterator = $this->$_yc_meta[$arr_lval];
			} else {
				// assert fail
				return NULL;
			}
		}

		return $iterator;
	}

	public function __destruct() {
		if(is_object($_yc_parent) && $this->$_yc_uuid != 0) {
			$this->$_yc_parent->ycUnregisterChild($this->$_yc_uuid);
		}

	}

	public function __get($lval) {
		if(isset($this->$_yc_metadex[$lval])) {
			return $this->$_yc_metadex[$lval];
		} else {
			return NULL;
		}
	}

	public function __set($lval,$rval) {
		$this->$_yc_metadex[$lval] = $rval;
	}

	public function __isset($lval) {
		return isset($this->$_yc_metadex[$lval]);
	}

	public function __unset($lval) {
		if(isset($this->$_yc_metadex[$lval])) unset($this->$_yc_metadex[$lval]);
	}

	public function __call($fname, $fargs) {
		if(isset($this->$_yc_metadex[$fname]) && is_callable($this->$_yc_metadex[$fname])) {
			return call_user_func_array($fname, $fargs);
		} else {
			return false;
		}
	}

	public static function __callStatic($fname, $fargs) {
		if(isset($this->$_yc_metadex[$fname]) && is_callable($this->$_yc_metadex[$fname])) {
			return forward_static_call_array($fname, $fargs);
		} else {
			return false;
		}
	}

	// Object index & attribute management functions ////////////////////////////////////
	// 
	//	** Do not overload these functions. They have been given 'final' designation
	//	   in order to prevent extended classes from tainting the resource tracking.
	// 
	
	/**
	 *  _yc_gen_uuid()
	 *  Generate and (by default) set object UUID for indexing and resource management.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @param      boolean       $xset     Set internal UUID after generation;
	 *                                      Pass (false) to disable
	 *  @default				  $xset		true
	 *  @return     string                  Object UUID string
	 * 
	 */
	final protected function _yc_gen_uuid($xset=true) {
		$newid = intval(microtime(true) * 1000000 * mt_rand());
		if($xset) $this->$_yc_uuid = $newid;
		return $_yc_uuid;
	}

	/**
	 *  _yc_get_uuid()
	 *  Return object/class UUID
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     string                  Object UUID string
	 * 
	 *  For the root (top-most) object, UUID will be equal to zero (0).
	 * 
	 */
	final public function _yc_get_uuid() {
		return $this->$_yc_uuid;
	}

	/**
	 *  _yc_get_depth()
	 *  Return object/class tree recursion depth
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     string                  Object UUID string
	 * 
	 *  For the root (top-most) object, UUID will be equal to zero (0).
	 * 
	 */
	final public function _yc_get_depth() {
		return $this->$_yc_depth;
	}

	/**
	 *  _yc_get_parent()
	 *  Return parent object/class reference
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     object [ref]             Parent object reference
	 * 
	 *  For the root (top-most) object, the parent reference points to self.
	 * 
	 */
	final public function &_yc_get_parent() {
		return $this->$_yc_parent;
	}

	/**
	 *  _yc_get_root()
	 *  Return root object/class reference
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     object [ref]             Root object reference
	 * 
	 */
	final public function &_yc_get_root() {
		return $this->$_yc_root;
	}

	/**
	 *  ycRegisterChild()
	 *  Called by child objects to register themselves with the parent's index.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, base
	 * 
	 *  @param      object [ref]  $childref    Child (usually, caller's) object reference
	 *  @param      string        $c_uuid      Child UUID
	 * 
	 *  If this function is overloaded by extending classes, 'return parent::ycRegisterChild()'
	 *  should be called!
	 * 
	 */
	public function ycRegisterChild(&$childref,$c_uuid) {
		$this->$_yc_children[$c_uuid] &= $childref;
	}

	/**
	 *  ycUnregisterChild()
	 *  Called by child objects upon destruction to unregister themselves from
	 *  the parent's index.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, base
	 * 	 
	 *  @param      string        $c_uuid      Child UUID
	 * 
	 *  If this function is overloaded by extending classes, 'return parent::ycUnregisterChild()'
	 *  should be called!
	 * 
	 */
	public function ycUnregisterChild($c_uuid) {
		unset($this->$_yc_children[$c_uuid]);
	}

}

class ycbase {
	public static $_yc_tracer = 'ycbase';
	protected $_yc_uuid;
	protected $_yc_root;
	protected $_yc_parent;
	protected $_yc_xopt;
	protected $_yc_children;
	protected $_yc_assert_last;

	// Constructors & Destructors ///////////////////////////////////////////////////////
	public static function __construct(&$pclx=NULL,$xopt=false) {
		if(!is_object($pclx) && !is_callable($pclx) && $pclx === NULL) {
			$_yc_parent = $this;
			$_yc_root = $this;
			$_yc_uuid = 0;
			$_yc_children = Array();
			$GLOBALS['_YC']['__root'] &= $this;
		} else {
			$this->_yc_gen_uuid();
			$this->$_yc_parent = $pclx;
			$this->$_yc_root = $pclx->_yc_get_root();
			$pclx->ycRegisterChild($this,$this->$_yc_uuid);
		}
		$this->$_yc_xopt = $xopt;
	}

	public static function __destruct() {
		if(is_object($_yc_parent) && $_yc_uuid != 0) {
			$_yc_parent->ycUnregisterChild($this->$_yc_uuid);
		}

	}
	
	// Object index & attribute management functions ////////////////////////////////////
	// 
	//	** Do not overload these functions. They have been given 'final' designation
	//	   in order to prevent extended classes from tainting the resource tracking.
	// 
	
	/**
	 *  _yc_gen_uuid()
	 *  Generate and (by default) set object UUID for indexing and resource management.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @param      boolean       $xset     Set internal UUID after generation;
	 *                                      Pass (false) to disable
	 *  @default				  $xset		true
	 *  @return     string                  Object UUID string
	 * 
	 */
	final protected function _yc_gen_uuid($xset=true) {
		$newid = intval(microtime(true) * 1000000 * mt_rand());
		if($xset) $this->$_yc_uuid = $newid;
		return $_yc_uuid;
	}

	/**
	 *  _yc_get_uuid()
	 *  Return object/class UUID
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     string                  Object UUID string
	 * 
	 *  For the root (top-most) object, UUID will be equal to zero (0).
	 * 
	 */
	final public function _yc_get_uuid() {
		return $this->$_yc_uuid;
	}

	/**
	 *  _yc_get_parent()
	 *  Return parent object/class reference
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     object [ref]             Parent object reference
	 * 
	 *  For the root (top-most) object, the parent reference points to self.
	 * 
	 */
	final public function &_yc_get_parent() {
		return $this->$_yc_parent;
	}

	/**
	 *  _yc_get_root()
	 *  Return root object/class reference
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, final, base
	 * 
	 *  @return     object [ref]             Root object reference
	 * 
	 */
	final public function &_yc_get_root() {
		return $this->$_yc_root;
	}

	/**
	 *  ycRegisterChild()
	 *  Called by child objects to register themselves with the parent's index.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, base
	 * 
	 *  @param      object [ref]  $childref    Child (usually, caller's) object reference
	 *  @param      string        $c_uuid      Child UUID
	 * 
	 *  If this function is overloaded by extending classes, 'return parent::ycRegisterChild()'
	 *  should be called!
	 * 
	 */
	public function ycRegisterChild(&$childref,$c_uuid) {
		$this->$_yc_children[$c_uuid] &= $childref;
	}

	/**
	 *  ycUnregisterChild()
	 *  Called by child objects upon destruction to unregister themselves from
	 *  the parent's index.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-11-25
	 *  @updated    2013-11-25
	 *  @flags      core, base
	 * 	 
	 *  @param      string        $c_uuid      Child UUID
	 * 
	 *  If this function is overloaded by extending classes, 'return parent::ycUnregisterChild()'
	 *  should be called!
	 * 
	 */
	public function ycUnregisterChild($c_uuid) {
		unset($this->$_yc_children[$c_uuid]);
	}


	// Core functions  //////////////////////////////////////////////////////////////////

	/**
	 *  yc_die()
	 *  Shutdown/exit callback function. Automatically hooked when yc_bstrap is included or loaded.
	 *  Decodes and displays any PHP parser, plugin, or subsystem errors if the shutdown was due to
	 *  an error.
	 *
	 *  @author     J. Hipps
	 *  @status     Complete
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      core, callback, no-return
	 *
	 */
	public static function yc_die() {

		$YC_PHP_ESTR =  Array(
					1	=> "E_ERROR",
					2	=> "E_WARNING",
					4	=> "E_PARSE",
					8	=> "E_NOTICE",
					16	=> "E_CORE_ERROR",
					32	=> "E_CORE_WARNING",
					64	=> "E_COMPILE_ERROR",
					128	=> "E_COMPILE_WARNING",
					256	=> "E_USER_ERROR",
					512	=> "E_USER_WARNING",
					1024	=> "E_USER_NOTICE",
					2048	=> "E_STRICT",
					4096	=> "E_RECOVERABLE_ERROR",
					8192	=> "E_DEPRECATED",
					16384	=> "E_USER_DEPRECATED",
					32767	=> "E_ALL"
				);

		$arglist = func_get_args();
		if(isset($arglist[0])) {
			if(gettype($arglist[0]) != "string") {
				$errno = $arglist[0];
				$fmode = $YC_PHP_ESTR[$errno];
			}
			$fmode = $arglist[0];
		}
		if(isset($arglist[1])) $fdesc = $arglist[1];

		fprintf($YC_ESTAT['stderr'],"\n\n***");
	}

	protected function _yc_assert_opt_set() {
		$aa['active'] = assert_options(ASSERT_ACTIVE,1);
		$aa['bail'] = assert_options(ASSERT_BAIL,1);
		$aa['callback'] = assert_options(ASSERT_CALLBACK,'ycbase::yc_assert_fail');
		$aa['bail'] = assert_options(ASSERT_BAIL,0);
		$this->$_yc_assert_last = $aa;
	}

	protected function _yc_assert_opt_restore() {
		$aa = $_yc_assert_last;

		if($aa) {
			assert_options(ASSERT_ACTIVE,$aa['active']);
			assert_options(ASSERT_BAIL,$aa['bail']);
			assert_options(ASSERT_CALLBACK,$aa['callback']);
			assert_options(ASSERT_BAIL,$aa['bail']);
		}
	}

	/**
	 *  yc_assert()
	 *  Assert sanity check. Wrapper for PHP's native assert().
	 *
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      core, debug
	 *
	 *  @param      boolean       $atest    Assertion test
	 *  @param      string        $atxt     Description/failure text for assertion compare
	 * 
	 *  @return     boolean                 Result of assertion (equal to $atest param)
	 */
	public static function yc_assert($atest,$atxt) {
		$this->_yc_assert_set();
		assert($atest,$atxt);
	}

	/**
	 *  yc_assert_fail()
	 *  Callback for debug assertions to allow the script to exit gracefully and display the point
	 *  at which the assertion failed, along with additional debug info, if necessary.
	 *
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      core, callback, no-return
	 *
	 *  @param      [type]        $afile    [description]
	 *  @param      [type]        $aline    [description]
	 *  @param      [type]        $acode    [description]
	 *  @param      [type]        $adesc    [description]
	 *  @return     [type]                  [description]
	 */
	public static function yc_assert_fail($afile, $aline, $acode, $adesc = NULL) {

		$tracer = debug_backtrace();
		$atracer = Array($afile,$aline,$acode,$adesc);

		$this->yc_die("assert",$tracer,$atracer);
	}

	/**
	 *  gen_xopt_regex()
	 *  Generates regex statements which correspond to the script's argument parsing requirements.
	 *  Extracts CLI arguments, switches, and other data.
	 *
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      none
	 *
	 *  @return     array        Associative array containing results of command line parsing
	 */
	public static function gen_xopt_regex() {
		global $YC_ARGPREFIX;
		global $YC_ARGFMT;

		/*
		$DEF_VTYPES =	Array(
					'str' => 'strval',
					'array' => 
		*/

		// vararg (DEF_XARGS) func input paramater schema definition
		$DEF_XARGS =	Array(
					0 => Array('var' => 'opt_name', 'vtype' => 'str', 'default' => '[eval]', 'eval' => 'return key($YC_ARGFMT);'),
					1 => Array('var' => 'optlist', 'vtype' => 'array', 'default' => '[eval]', 'eval' => 'return current($YC_ARGFMT)[\'optlist\']);'),
					2 => Array('var' => 'ignorecase', 'vtype' => 'bool', 'default' => false),
					3 => Array('var' => 'prefix_optional', 'vtype' => 'bool', 'default' => false),
					4 => Array('var' => 'DEBUG_PARAMS', 'next_items' => -1, 'vtype' => 'mixed', 'default' => NULL)
				);

		// get arg list into array
		$arglist = func_get_args();

		foreach($DEF_XARGS as $adex => $adef) {
			if(isset($arglist[$adex])) {
				// map varname to actual local var using varying var names...
				unset(${$adef['var']});
				${$adef['var']} = NULL;

				// assign default value, if needed
				if(isset($adef['default']) && $adef['default'] !== NULL) {
					if(strtolower($adef['default']) == "[eval]") {
						${$adef['var']} = eval($adef['eval']);
					} else if(strtolower($adef['default']) == "[func]") {
						if(isset($adef['func']) && iscallable($adef['func'])) {
							${$adef['var']} = call_user_func($adef['func']);
						}
					} else {
						${$adef['var']} = $adef['default'];
					}
				}
			}
		}

		
		$opt = "";
		if($ignorecase) $opt = "i";

		$opt_prefix_lst = '(?:';
		foreach($YC_ARG_PREFIX as $idex => $pfx) {
			if($idex) $sepx = '|';
			else $sepx = "";
			$opt_prefix_lst .= $sepx.preg_quote($pfx);
		}
		$opt_prefix_lst .= ')'.($prefix_optional != false ? '?' : '');

		$reggie = '/^(?:'.$opt_prefix_lst.$opt_name_list.')(?:\=|\s+)(?:(?:\\\'(?<out_fmt1>[^\\\']+)\\\')|(?:\"(?<out_fmt2>[^\"]+)\"))\s*$/'.$opt;

		return $reggie;
	}

	/**
	 *  zecho()
	 *  Wraps the PHP echo() command so that it can be redirected if needed. Used for logging and verbosity control.
	 *
	 *  @author     J. Hipps
	 *  @status     Working
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      core
	 *
	 *  @param      string        $s    Text to be logged or echoed to console/output device
	 * 
	 */
	public static function zecho($s) {
		global $TX_OUTPUT;

		if(!$TX_OUTPUT) $TX_OUTPUT = STDOUT;
		fprintf($TX_OUTPUT,$s);
	}

	/**
	 *  yc_read_args()
	 *  Parse argument list from $argv; Check against YC metadata array containing argument parsing rules
	 *
	 *  @author     J. Hipps
	 *  @status     Incomplete
	 *  @added      2013-08-15
	 *  @updated    2013-08-15
	 *  @flags      none
	 *
	 *  @return     array        Associative array of arguments passed to script
	 */	
	public static function yc_read_args() {
		global $YC_ARGFMT;

		foreach($argv as $aloc => $aval) {
			$arglist[$aloc]['raw'] = $aval;
			$arglist[$aloc]['read'] = false;
		}

		foreach($YC_ARGFMT as $argname => $parsex) {
			foreach($argv as $aloc => $aval) {
				
			}
		}
	}


	/**
	 *  yc_get_prog_path()
	 *  Returns information about the program/binary in question
	 *
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-10-10
	 *  @updated    2013-10-10
	 *  @flags      none
	 *
	 *  @param      string        $binname    Name of program binary
	 *  @param_opt  enum          $outtype    Type of data to return to caller (YC_PROGPATH_OUTTYPE)
	 *  @default				  $outtype	  YC_PROGPATH_ARRAY = 0
	 * 
	 *  @return     mixed        Information about the binary; content and type determined by $outtype. Default is
	 *  					     associative array with 'realpath' and 'bin' string members.
	 * 
	 *  @enumdef		$outtype				YC_PROGPATH_OUTTYPE
	 *	@econst			YC_PROGPATH_ARRAY		return assoc. array [array/assoc; default]
	 *					      					Contains 'realpath' string (eg. "/usr/bin/php") and
	 *  							       		'bin' (eg. "php") members at the top-level of the array
	 *	@econst			YC_PROGPATH_RPSTR		return realpath string [string]
	 *	@econst			YC_PROGPATH_BINSTR		return binary name string [string]
	 *	@econst			YC_PROGPATH_EXISTS		only return true/false based on if prog exists or 'whereis' command fails [bool]
	 * 
	 */	
	public static function yc_get_prog_path($binname,$outtype=YC_PROGPATH_ARRAY) {
		$clx = "whereis -b '$binname'";

		$whereru = trim(shell_exec($clx));
		if(!preg_match("^([^:]+): ([^ ]+(?:\/([^\/ ]+))).*$",$whereru,$whatdex)) {		
			$xout = false;
		} else {
			$binpath = $whatdex[0][1];
			$binname = $whatdex[0][2];
			$xout = Array('realpath' => $binpath, 'bin' => $binname);
		}

		return $xout;
	}

	/**
	 *  YCDBG_doandsay()
	 *  Takes a PHP statement, prints it to stdout, then runs eval() on it
	 *
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-10-10
	 *  @updated    2013-10-10
	 *  @flags      debug
	 *  @param      mixed         $do     Statement to execute and echo to stdout
	 *  @param_opt  string        $say    Optional comment; echoed before statement
	 *  @default				  $say	  (null)
	 *                                    
	 */
	public static function YCDBG_doandsay($do,$say="") {

		if($say) echo ">> // $say\n";
		echo ">> [ $do ]\n";
		echo "#>\t\t";
		$rvx = eval($do);
		echo "\n>> <OK>\n\n";
		
		return $rvx;
	}

	/**
	 *  YCDBG_vvar() Pretty-print a var to stdout
	 *  @author     J. Hipps
	 *  @status     WIP
	 *  @added      2013-10-10
	 *  @updated    2013-10-10
	 *  @flags      debug
	 * 
	 *  @param      string        $xn     Name of var or operation
	 *  @param_opt  mixed         $xv     Value
	 *  @default				  $xv	  (false). In vitro, set to value of given variable; imported using 'global'
	 *  @param_opt  string        $pfx    Var prefix
	 *  @default				  $pfx    "$" 
	 */
	public static function YCDBG_vvar($xn,$xv=false,$pfx="\$") {

		if($xn[0] != "\$") $zname = $pfx.$xn;
		else $zname = $xn;

		$zval = $xv;

		echo "\t[ ".ANSI_RED.$zname.ANSI_WHT." = ".ANSI_CYN.$zval.ANSI_OFF." ]\n";

	}


}

interface ycParserObj {

}

class yc_parser {

}

class yc_parser_http extends yc_parser {

	public static $_yc_tracer = 'ycbase\\yc_parser\\yc_parser_http';
	private $_yc_uuid;
	private $_yc_xparam;
	private static $_yc_defaults = Array('parser_drx' => 'this->parserDrx','token_dex' => 'this->tokenList');

	private $parserDrx =	Array(
			                     	'_parser' =>	Array(
			                     	             			'_top' =>			Array(
						                     	             			          	'type' => 'section_match/single',
						                     	             			          	'input' => '_raw_http_data',
						                     	             			          	'output' => Array('http_ver','http_req_mode','http_req_uri','http_stat_code','http_stat_msg','http_content'),
						                     	             			          	'xpass' => 'header_list',
						                     	             			          	'rgx_opt' => 'is',
						                     	             			          	'rgx' => '^(?<http_ver>HTTP\/[0-9\.]{1,5}) ((?<http_req_mode>[A-Z_-]+) (?<http_req_uri>.+?)|(?<http_stat_code>[0-9]{3}) (?<http_stat_msg>.+?))[\n\r]{1,2}(?<header_list>.+?)[\n\r]{2,4}(?<http_content>.+)$'
						                     	             			          ),
			                     	             			'header_list' =>	Array(
						                     	             			          	'_pragma' => Array('_mode' => 'tokenize'),
						                     	             			          	'type' => 'list_tokenize',
						                     	             			          	'input' => 'header_list',
						                     	             			          	'output' => Array('header_lval','header_rval'),            	             			          	
						                     	             			          	'rgx_opt' => 'im',
						                     	             			          	'rgx' => '^(?<header_lval>.+?):(?<header_rval>.+?)$',
						                     	             			          	'callback' => 'this::tok_http_header'
						                     	             			          )
			                     	             		)
							);

	private $tokenList =	Array(
				                    'header_lval' => 	Array(
				                                            // general (request/response)
					                	                	'Host',
															'Connection',
									                    	'Content-Type',
									                    	'Content-Length',
									                    	'Content-Encoding',
									                    	'Content-Language',
									                    	'Content-Location',
									                    	'Content-Range',
									                    	'Content-MD5',
									                    	'Date',
									                    	'Pragma',
									                    	'ETag',

									                    	'Trailer',
									                    	'Transfer-Encoding',
									                    	'Upgrade',
									                    	'Warning',

										                    // request
									                    	'Accept',
									                    	'Accept-Charset',
									                    	'Accept-Language',
									                    	'Accept-Encoding',
									                    	'Authorization',
									                    	'Range',
									                    	'Referer',
									                    	'User-Agent',
									                    	'Via',						                    	
									                    	'From',
									                    	'Cookie',

									                    	'TE',

									                    	'Expect',
									                    	'If-Match',
									                    	'If-Modified-Since',
									                    	'If-None-Match',
									                    	'If-Range',
									                    	'If-Unmodified-Since',
									                    	'Max-Forwards',

									                    	'Proxy-Connection',
									                    	'Proxy-Authorization',

									                    	'X-Requested-With',
									                    	'X-Forwarded-For',

														// response
									                 		'Age',
									                 		'Allow',						                 		
									                 		'Accept-Ranges',
									                 		'Location',
									                 		'Link',

									                 		'Last-Modified',
									                 		'Cache-Control',
									                 		'Expires',

									                 		'Set-Cookie',

									                 		'Proxy-Authenticate',

									                 		'Retry-After',
									                 		'Server',
									                 		'Vary',
									                 		'WWW-Authenticate',

									                 		'X-Robots-Tag',
									                 		'X-XSS-Protection',
									                 		'X-Powered-By',
									                 		'X-UA-Compatible',

									                 		'X-Sdch-Encode'

									                 	)
					);

	public static function __construct(&$pclx=NULL,$xopt=false) {
		
		if(is_array($xopt)) {
			$this->$_yc_xparam = $opt_params;
		} else {
			$this->$_yc_xparam &= $_yc_defaults;
		}

		return $parent->__construct($pclx);
	}

	private function tok_http_header($lval,$rval) {

	}

	public function yc_parse_request_hdr($hdrdata=false,$xflags=YC_PARSE_REQHDR_AA) {
		if($hdrdata === false || $hdrdata === NULL) {		
			if(function_exists('getallheaders')) {
				$prh['enum_src'] = 'getallheaders()';
				$prh['headers'] = getallheaders();
			} else {
				$prh['enum_src'] = '_SERVER';

				foreach ($_SERVER as $name => $value) { 
					if (substr($name, 0, 5) == 'HTTP_') {
						$prh['headers'][str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					}
				}
			}
		} else {
			if($xflags & YC_PARSE_REQHDR_RAW) {
				$prh['enum_src'] = 'http_parse_headers()';
				$prh['headers'] = http_parse_headers($hdrdata);
			} else {
				$prh['enum_src'] = 'raw/array';
				$prh['headers'] = $hdrdata;
			}
		}

		foreach($prh['headers'] as $hkx => $hval) {

		}
	}
}

function yc_get_whirlpool_ver($pmt=0) {

	$kck = Array(
				'in' => Array(0 => ""),
				'algo' => Array(0 => 'whirlpool'),
				'mdex' => Array(
								'whirlpool0' => Array(0 => "B3E1AB6EAF640A34F784593F2074416ACCD3B8E62C620175FCA0997B1BA2347339AA0D79E754C308209EA36811DFA40C1C32F1A2B9004725D987D3635165D3C8"),
								'whirlpool1' => Array(0 => "470F0409ABAA446E49667D4EBE12A14387CEDBD10DD17B8243CAD550A089DC0FEEA7AA40F6C2AAAB71C6EBD076E43C7CFCA0AD32567897DCB5969861049A0F5A"),
								'whirlpool2' => Array(0 => "19FA61D75522A4669B44E39C1D2E1726C530232130D407F89AFEE0964997F7A73E83BE698B288FEBCF88E3E03C4F0757EA8964E59B63D93708B138CC42A66EB3")
			 				)
			  );

	$pltxt = $kck['in'][$pmt];
	$halgo = $kck['algo'][$pmt];
	$hout = strtolower(trim(hash($halgo,$pltxt)));

	$ver = false;

	foreach($kck['mdex'] as $aver => $ahash) {
		if(is_array($ahash)) $xa = strtolower($ahash[$pmt]);
		else $xa = strtolower($ahash);

		if(!strcasecmp($xa,$ahash)) {
			$ver = $aver;
		}
	}


	return $ver;
}


?>