<?php /* Thanks goes to Cal Henderson at iamcal.com for the great php search script example (http://www.iamcal.com/publish/articles/php/search/). */

/**
* Search
*
* The Search Class is used to search through a field in a MYSQL database for matching (or similar) text.
*
*/
class Search
{
	/*** data members ***/

	//private static $search_obj;
	private $search_terms=NULL;
	protected $tables;
	protected $fields;
	protected $id_names;
	protected $all_results=NULL;

	/*** End data members ***/



	/*** magic methods ***/

	/**
	* __construct
	*
	* @param	$table (An array of the tables we're searching in.)
	* @param	$fields (An array of the fields we're searching in.)
	* @param	$id (An array of the names of the id fields in the tables we are searching.)
	* @access	public
	*/
	public function __construct($tables=NULL, $fields=NULL, $id_names='id')
	{
		$this->setTable($tables);
		$this->setFields($fields);
		$this->setID_Names($id_names);
		return;
	}

	/*** End magic methods ***/



	/*** mutator methods ***/

	/**
	 * setSearchTerms
	 *
	 * Sets the data member $search_terms.
	 *
	 * @param	$search_terms
	 * @access	public
	 */
	public function setSearchTerms($search_terms)
	{
		# Set the variable.
		$this->search_terms=$search_terms;
	} #=== End -- setSearchTerms

	/**
	 * setTables
	 *
	 * Sets the data member $tables.
	 *
	 * @param		$tables (An array of the tables to search.)
	 * @access	public
	 */
	public function setTable($tables)
	{
		# Set the variable.
		$this->tables=$tables;
	} #==== End -- setTables

	/**
	 * setFields
	 *
	 * Sets the data member $fields.
	 *
	 * @param		$fields (An array of the fields to search.)
	 * @access	public
	 */
	public function setFields($fields)
	{
		# Set the variable.
		$this->fields=$fields;
	} #==== End -- setFields

	/**
	 * setID_Names
	 *
	 * Sets the data member $id_names.
	 *
	 * @param		$id_names (The name of the id fields in the tables to search.)
	 * @access	public
	 */
	public function setID_Names($id_names)
	{
		# Set the variable.
		$this->id_names=$id_names;
	} #==== End -- setID_Names

	/**
	 * setAllResults
	 *
	 * Sets the data member $all_results.
	 *
	 * @param		$all_results (The results or the search.)
	 * @access	public
	 */
	public function setAllResults($all_results)
	{
		# Set the variable.
		$this->all_results=$all_results;
	} #==== End -- setAllResults

	/*** End mutator methods ***/



	/*** accessor methods ***/

	/**
	 * getSearchTerms
	 *
	 * Returns the data member $search_terms.
	 *
	 * @access	public
	 */
	public function getSearchTerms()
	{
		return $this->search_terms;
	} #==== End -- getSearchTerms

	/**
	 * getTables
	 *
	 * Returns the data member $tables.
	 *
	 * @access	protected
	 */
	protected function getTables()
	{
		return $this->tables;
	} #==== End -- getTables

	/**
	 * getFields
	 *
	 * Returns the data member $fields.
	 *
	 * @access	protected
	 */
	protected function getFields()
	{
		return $this->fields;
	} #==== End -- getFields

	/**
	 * getID_Names
	 *
	 * Returns the data member $id_names.
	 *
	 * @access	protected
	 */
	protected function getID_Names()
	{
		return $this->id_names;
	} #==== End -- getID_Names

	/**
	 * getAllResults
	 *
	 * Returns the data member $all_results.
	 *
	 * @access	public
	 */
	public function getAllResults()
	{
		return $this->all_results;
	} #==== End -- getAllResults

	/*** End accessor methods ***/



	/*** public methods ***/

	/**
	 * processSearch
	 *
	 * Checks if the search form has been submitted and processes it, returning the results of the search.
	 *
	 * @param	$index					The term we're searching for.
	 * @param	$filter					Fields and or terms we would like exluded.
	 * @access	public
	 */
	public function processSearch($index='searchterms', $filter=NULL)
	{
		# Check if the search form has been submitted.
		if(array_key_exists('_submit_check', $_POST))
		{
			$tables=$this->getTables();
			$fields=$this->getFields();
			$id=$this->getID_Names();
			$terms=$_POST[$index];
			$search_results=array();
			foreach($tables as $table)
			{
				$results[$table]=$this->performSearch($terms, $table, $fields[$table], $id[$table], $filter=NULL);
				$search_results=array_merge($search_results, $results);
			}
			$this->setAllResults($search_results);
		}
	} #==== End -- processSearch

	/**
	 * displayResults
	 *
	 * Displays the results of the search.
	 *
	 * @param		$terms (The term we're searching for.)
	 * @param		$filter (Fields and or terms we would like exluded.)
	 * @access	public
	 */
	public function displayResults($fields, $display_field)
	{
		# Set the Database instance to a variable.
		$db=DB::get_instance();

		# Check if the search form has been submitted.
		if(array_key_exists('_submit_check', $_POST))
		{
			$tables=$this->getTables();
			$id=$this->getID_Names();
			$all_results=$this->getAllResults();
			$display_results='Your search returned ';
			$display_list='<ul>';
			$result_count=0;
			if($all_results!==NULL)
			{
				foreach($tables as $table)
				{
					$result_count=$result_count+count($all_results[$table]);
					foreach($all_results[$table] as $result_id)
					{
						$num_select_fields=count($fields[$table]);
						$select_fields=implode('`, `', $fields[$table]);
						$results[$table]=$db->get_row('SELECT `'.$select_fields.'` FROM `'.$table.'` WHERE `'.$id[$table].'` = '.$db->quote($db->escape($result_id->$id[$table])));
						$display_list.='<li>';
						for($i=0; $i<$num_select_fields; $i++)
						{
							$display_search_results[$result_id->$id[$table]]=array($fields[$table][$i]=>$results[$table]->$fields[$table][$i]);
							$display_list.=$results[$table]->$fields[$table][$i];
						}
						$display_list.='</li>';
					}
				}
			}
			$display_search_results['result_count']=$result_count;
			$display_results.=$result_count.' results:';
			$display_results.=$display_list;
			$display_results.='</ul>';
			return $display_search_results;
		}
	} #==== End -- displayResults

	/**
	* performSearch
	*
	* Returns the results of the search
	*
	* @param	$terms					The term we're searching for.
	* @param	$table					The table we're searching in.
	* @param	$fields					The fields we're searching in.
	* @param	$id						The name of the id field in the table we are searching.
	* @param	$filter					Fields and or terms we would like exluded.
	* @access	public
	*/
	public function performSearch($terms, $table, $fields, $id='id', $filter=NULL)
	{
		# Set the Database instance to a variable.
		$db=DB::get_instance();

		$where=$this->prepareWhere($terms, $fields, $filter);

		//$sql="SELECT `id` FROM `users` WHERE `Party` = 'yes' AND `Username` RLIKE '%Joey%' OR `fname` RLIKE '%Joey%';
		$sql='SELECT '.$id.' FROM `'.$table.'` WHERE '.$where;
		$search_results=(array)$db->get_results($sql);
		return $search_results;
	} #==== End -- performSearch

	/*** End public methods ***/



	/*** protected methods ***/

/*** NEEDS MORE WORK (add more characters ie tilde n, accented e, a, o, etc) ***/
	/**
	* splitTerms
	*
	* Splits the string ($terms) and puts each searchable term into an array.
	* Returns an array of search terms based on the string ($terms).
	*
	* @param	$terms (The string splitting)
	* @access	protected
	*/
	protected function splitTerms($terms)
	{
		# Explicitly make $terms an array.
		$terms=(array)$terms;

		# Create new array for our output.
		$out=array();
		# Create a new array for our alternate terms.
		$alt_terms=array();
		# Create a new array for our interim output.
		$interim_out=array();

		# Define excluded words.
		$exclude=' the if it to a I but no so of are and';

		# Creat a variable to hold the reg ex pattern that finds all pair of double quotes (").
		$pattern='/\"(.*?)\"/e';
		# Creat a variable to hold the method call that replaces any whitespaces or commas with a holder token.
		$replacement="Search::change2Token('\$1')";
		# Find all pair of double quotes (") and pass their contents to the change2Token() method for processing.
		$terms=preg_replace($pattern, $replacement, $terms);
		# Take out parentheses
		$terms=preg_replace('/\)|\(/', '', $terms);

		# Loop through the terms
		foreach($terms as $term)
		{
			if(!empty($term))
			{
				# Split searchable terms on whitespace and commas and put into an array.
				$term=preg_split("/\s+|,/", $term);
				foreach($term as $split)
				{
					# If the term is not in the excluded list add it to the $interim_out array.
					if(!empty($split) && (strpos($exclude, $split)===FALSE))
					{
						$interim_out[]=$split;
					}
				}
			}
		}
		# Rename $interim_out as $terms.
		$terms=$interim_out;

		# Loop through the array.
		foreach($terms as $term)
		{
			# For each searchable term, replace the holding tokens with their original contents (whitespace or comma).
			$term=preg_replace("/\{WHITESPACE-([\d]+)\}/e", "chr('\$1')", $term);
			$term=preg_replace("/\{COMMA\}/", ",", $term);

			# If the term is not in the excluded list add it to the $out array.
			if(!empty($term) && (strpos($exclude, $term)===FALSE))
			{
				$out[]=$term;
			}
		}
		# Loop through the array again.
		foreach($out as $term)
		{
			# First, replace HTML entities
			$dblquote_search='/&(ldquo|#8220|rdquo|#8221|quot|#34|#034|#x22);/i';
			$search[]=$dblquote_search;
			$snglquote_search='/&(lsquo|#8216|rsquo|#8217);/i';
			$search[]=$snglquote_search;
			$dash_search='/&(ndash|#x2013|#8211|mdash|#x2014|#8212|#150);/i';
			$search[]=$dash_search;
			$ampersand_search='/&(amp|#38|#038|#x26);/i';
			$search[]=$ampersand_search;
			$lessthan_search='/&(lt|#60|#060|#x3c);/i';
			$search[]=$lessthan_search;
			$greaterthan_search='/&(gt|#62|#062|#x3e);/i';
			$search[]=$greaterthan_search;
			$space_search='/&(nbsp|#160|#xa0);/i';
			$search[]=$space_search;
			$inverted_exclamation_mark_search='/&(iexcl|#161);/i';
			$search[]=$inverted_exclamation_mark_search;
			$inverted_question_mark_search='/&(iquest|#191);/i';
			$search[]=$inverted_question_mark_search;
			$cent_search='/&(cent|#162);/i';
			$search[]=$cent_search;
			$pound_search='/&(pound|#163);/i';
			$search[]=$pound_search;
			$copyright_search='/&(copy|#169);/i';
			$search[]=$copyright_search;
			$registered_search='/&(reg|#174);/i';
			$search[]=$registered_search;
			$degrees_search='/&(deg|#176);/i';
			$search[]=$degrees_search;
			$apostrophe_search='/&(apos|#39|#039|#x27);/';
			$search[]=$apostrophe_search;
			$euro_search='/&(euro|#8364);/i';
			$search[]=$euro_search;
			$umlaut_a_search='/&a(uml|UML);/';
			$search[]=$umlaut_a_search;
			$umlaut_o_search='/&o(uml|UML);/';
			$search[]=$umlaut_o_search;
			$umlaut_u_search='/&u(uml|UML);/';
			$search[]=$umlaut_u_search;
			$umlaut_y_search='/&y(uml|UML);/';
			$search[]=$umlaut_y_search;
			$umlaut_A_search='/&A(uml|UML);/';
			$search[]=$umlaut_A_search;
			$umlaut_O_search='/&O(uml|UML);/';
			$search[]=$umlaut_O_search;
			$umlaut_U_search='/&U(uml|UML);/';
			$search[]=$umlaut_U_search;
			$umlaut_Y_search='/&Y(uml|UML);/';
			$search[]=$umlaut_Y_search;
			$latin_small_letter_sharp_s_search='/&(szlig|#xdf|#223);/i';
			$search[]=$latin_small_letter_sharp_s_search;

			$dblquote_replace=chr(34);
			$replace[]=$dblquote_replace;
			$snglquote_replace="'";
			$replace[]=$snglquote_replace;
			$dash_replace=chr(45);
			$replace[]=$dash_replace;
			$ampersand_replace=chr(38);
			$replace[]=$ampersand_replace;
			$lessthan_replace=chr(60);
			$replace[]=$lessthan_replace;
			$greaterthan_replace=chr(62);
			$replace[]=$greaterthan_replace;
			$space_replace=' ';
			$replace[]=$space_replace;
			$inverted_exclamation_mark_replace='¡';
			$replace[]=$inverted_exclamation_mark_replace;
			$inverted_question_mark_replace='¿';
			$replace[]=$inverted_question_mark_replace;
			$cent_replace='¢';
			$replace[]=$cent_replace;
			$pound_replace='£';
			$replace[]=$pound_replace;
			$copyright_replace='©';
			$replace[]=$copyright_replace;
			$registered_replace='®';
			$replace[]=$registered_replace;
			$degrees_replace='°';
			$replace[]=$degrees_replace;
			$apostrophe_replace=chr(39);
			$replace[]=$apostrophe_replace;
			$euro_replace='€';
			$replace[]=$euro_replace;
			$umlaut_a_replace='ä';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace="ö";
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace="ü";
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace="ÿ";
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace="Ä";
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace="Ö";
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace="Ü";
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace="Ÿ";
			$replace[]=$umlaut_Y_replace;
			$latin_small_letter_sharp_s_replace="ß";
			$replace[]=$latin_small_letter_sharp_s_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Second, replace with HTML entities
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$snglquote_search="'";
			$search[]='/('.$snglquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';
			$ampersand_search=chr(38);
			$search[]='/('.$ampersand_search.')/';
			$lessthan_search=chr(60);
			$search[]='/('.$lessthan_search.')/';
			$greaterthan_search=chr(62);
			$search[]='/('.$greaterthan_search.')/';
			$space_search=' ';
			$search[]='/('.$space_search.')/';
			$inverted_exclamation_mark_search='¡';
			$search[]='/('.$inverted_exclamation_mark_search.')/';
			$inverted_question_mark_search='¿';
			$search[]='/('.$inverted_question_mark_search.')/';
			$cent_search='¢';
			$search[]='/('.$cent_search.')/';
			$pound_search='£';
			$search[]='/('.$pound_search.')/';
			$copyright_search='©';
			$search[]='/('.$copyright_search.')/';
			$registered_search='®';
			$search[]='/('.$registered_search.')/';
			$degrees_search='°';
			$search[]='/('.$degrees_search.')/';
			$apostrophe_search=chr(39);
			$search[]='/('.$apostrophe_search.')/';
			$euro_search='€';
			$search[]='/('.$euro_search.')/';
			$umlaut_a_search='ä|a(^(&a(uml|UML)))';
			$search[]='/('.$umlaut_a_search.')/';
			$umlaut_o_search='ö|o(^(&o(uml|UML)))';
			$search[]='/('.$umlaut_o_search.')/';
			$umlaut_u_search='ü|u(^(&u(uml|UML)))';
			$search[]='/('.$umlaut_u_search.')/';
			$umlaut_y_search='(^(&y(uml|UML))])|ÿ|y';
			$search[]='/('.$umlaut_y_search.')/';
			$umlaut_A_search='Ä|A(^(&A(uml|UML)))';
			$search[]='/('.$umlaut_A_search.')/';
			$umlaut_O_search='Ö|O(^(&O(uml|UML)))';
			$search[]='/('.$umlaut_O_search.')/';
			$umlaut_U_search='Ü|U(^(&U(uml|UML)))';
			$search[]='/('.$umlaut_U_search.')/';
			$umlaut_Y_search='Ÿ|Y(^(&Y(uml|UML)))';
			$search[]='/('.$umlaut_Y_search.')/';
			$latin_small_letter_sharp_s_search='ß';
			$search[]='/('.$latin_small_letter_sharp_s_search.')/';

			$dblquote_replace='&ldquo;';
			$replace[]=$dblquote_replace;
			$snglquote_replace='&lsquo;';
			$replace[]=$snglquote_replace;
			$dash_replace='&ndash;';
			$replace[]=$dash_replace;
			$ampersand_replace='&amp;';
			$replace[]=$ampersand_replace;
			$lessthan_replace='&lt;';
			$replace[]=$lessthan_replace;
			$greaterthan_replace='&gt;';
			$replace[]=$greaterthan_replace;
			$space_replace='&nbsp;';
			$replace[]=$space_replace;
			$inverted_exclamation_mark_replace='&iexcl;';
			$replace[]=$inverted_exclamation_mark_replace;
			$inverted_question_mark_replace='&iquest;';
			$replace[]=$inverted_question_mark_replace;
			$cent_replace='&cent;';
			$replace[]=$cent_replace;
			$pound_replace='&pound;';
			$replace[]=$pound_replace;
			$copyright_replace='&copy;';
			$replace[]=$copyright_replace;
			$registered_replace='&reg;';
			$replace[]=$registered_replace;
			$degrees_replace='&deg;';
			$replace[]=$degrees_replace;
			$apostrophe_replace='&apos;';
			$replace[]=$apostrophe_replace;
			$euro_replace='&euro;';
			$replace[]=$euro_replace;
			$umlaut_a_replace='&auml;';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace='&ouml;';
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace='&uuml;';
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace='&yuml;';
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace='&Auml;';
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace='&Ouml;';
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace='&Uuml;';
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace='&Yuml;';
			$replace[]=$umlaut_Y_replace;
			$latin_small_letter_sharp_s_replace='&szlig;';
			$replace[]=$latin_small_letter_sharp_s_replace;

			$alt_term=preg_replace($search, $replace, $term);

			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search array.
			$replace=array();

			# Third with alternate HTML entities
			$dblquote_replace='&#8220;';
			$replace[]=$dblquote_replace;
			$snglquote_replace='&#8216;';
			$replace[]=$snglquote_replace;
			$dash_replace='&#x2013;';
			$replace[]=$dash_replace;
			$ampersand_replace='&#38;';
			$replace[]=$ampersand_replace;
			$lessthan_replace='&#60;';
			$replace[]=$lessthan_replace;
			$greaterthan_replace='&#62;';
			$replace[]=$greaterthan_replace;
			$space_replace='&#160;';
			$replace[]=$space_replace;
			$inverted_exclamation_mark_replace='&#161;';
			$replace[]=$inverted_exclamation_mark_replace;
			$inverted_question_mark_replace='&#191;';
			$replace[]=$inverted_question_mark_replace;
			$cent_replace='&#162;';
			$replace[]=$cent_replace;
			$pound_replace='&#163;';
			$replace[]=$pound_replace;
			$copyright_replace='&#169;';
			$replace[]=$copyright_replace;
			$registered_replace='&#174;';
			$replace[]=$registered_replace;
			$degrees_replace='&#176;';
			$replace[]=$degrees_replace;
			$apostrophe_replace='&#39;';
			$replace[]=$apostrophe_replace;
			$euro_replace='&#8364;';
			$replace[]=$euro_replace;
			$umlaut_a_replace='&aUML;';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace='&oUML;';
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace='&uUML;';
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace='&yUML;';
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace='&AUML;';
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace='&OUML;';
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace='&UUML;';
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace='&YUML;';
			$replace[]=$umlaut_Y_replace;
			$latin_small_letter_sharp_s_replace='&#xdf;';
			$replace[]=$latin_small_letter_sharp_s_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Fourth, we do it again
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$snglquote_search="'";
			$search[]='/('.$snglquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';
			$ampersand_search=chr(38);
			$search[]='/('.$ampersand_search.')/';
			$lessthan_search=chr(60);
			$search[]='/('.$lessthan_search.')/';
			$greaterthan_search=chr(62);
			$search[]='/('.$greaterthan_search.')/';
			$space_search=' ';
			$search[]='/('.$space_search.')/';
			$apostrophe_search=chr(39);
			$search[]='/('.$apostrophe_search.')/';
			$latin_small_letter_sharp_s_search='ß';
			$search[]='/('.$latin_small_letter_sharp_s_search.')/';

			$dblquote_replace='&rdquo;';
			$replace[]=$dblquote_replace;
			$snglquote_replace='&rsquo;';
			$replace[]=$snglquote_replace;
			$dash_replace='&#8211;';
			$replace[]=$dash_replace;
			$ampersand_replace='&#038;';
			$replace[]=$ampersand_replace;
			$lessthan_replace='&#060;';
			$replace[]=$lessthan_replace;
			$greaterthan_replace='&#062;';
			$replace[]=$greaterthan_replace;
			$space_replace='&#xa0;';
			$replace[]=$space_replace;
			$apostrophe_replace='&#039;';
			$replace[]=$apostrophe_replace;
			$latin_small_letter_sharp_s_replace='&#223;';
			$replace[]=$latin_small_letter_sharp_s_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Fifth, again.
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$snglquote_search="'";
			$search[]='/('.$snglquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';
			$ampersand_search=chr(38);
			$search[]='/('.$ampersand_search.')/';
			$lessthan_search=chr(60);
			$search[]='/('.$lessthan_search.')/';
			$greaterthan_search=chr(62);
			$search[]='/('.$greaterthan_search.')/';
			$apostrophe_search=chr(39);
			$search[]='/('.$apostrophe_search.')/';

			$dblquote_replace='&#8221;';
			$replace[]=$dblquote_replace;
			$snglquote_replace='&#8217;';
			$replace[]=$snglquote_replace;
			$dash_replace='&mdash;';
			$replace[]=$dash_replace;
			$ampersand_replace='&#x26;';
			$replace[]=$ampersand_replace;
			$lessthan_replace='&#x3c;';
			$replace[]=$lessthan_replace;
			$greaterthan_replace='&#x3e;';
			$replace[]=$greaterthan_replace;
			$apostrophe_replace='&#x27;';
			$replace[]=$apostrophe_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Sixth, again
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';

			$dblquote_replace='&quot;';
			$replace[]=$dblquote_replace;
			$dash_replace='&#x2014;';
			$replace[]=$dash_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Seventh, again
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';

			$dblquote_replace='&#34;';
			$replace[]=$dblquote_replace;
			$dash_replace='&#8212;';
			$replace[]=$dash_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Eighth, once again
			$dblquote_search=chr(34);
			$search[]='/('.$dblquote_search.')/';
			$dash_search=chr(45);
			$search[]='/('.$dash_search.')/';

			$dblquote_replace='&#034;';
			$replace[]=$dblquote_replace;
			$dash_replace='&#150;';
			$replace[]=$dash_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Ninth, again

			$alt_term=preg_replace('/('.chr(34).')/', '&#x22;', $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Tenth, again
			$umlaut_a_search='/a&(^(&a(uml|UML)))/';
			$search[]=$umlaut_a_search;
			$umlaut_o_search='/o&(^(&o(uml|UML)))/';
			$search[]=$umlaut_o_search;
			$umlaut_u_search='/u&(^(&u(uml|UML)))/';
			$search[]=$umlaut_u_search;
			$umlaut_y_search='/(y[^(&y(uml|UML))])/';
			$search[]=$umlaut_y_search;
			$umlaut_A_search='/A&(^(&A(uml|UML)))/';
			$search[]=$umlaut_A_search;
			$umlaut_O_search='/O&(^(&O(uml|UML)))/';
			$search[]=$umlaut_O_search;
			$umlaut_U_search='/U&(^(&U(uml|UML)))/';
			$search[]=$umlaut_U_search;
			$umlaut_Y_search='/Y&(^(&Y(uml|UML)))/';
			$search[]=$umlaut_Y_search;

			$umlaut_a_replace='ä';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace="ö";
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace="ü";
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace="ÿ";
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace="Ä";
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace="Ö";
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace="Ü";
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace="Ÿ";
			$replace[]=$umlaut_Y_replace;
			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Eleventh, again
			$umlaut_a_search='/ä/';
			$search[]=$umlaut_a_search;
			$umlaut_o_search='/ö/';
			$search[]=$umlaut_o_search;
			$umlaut_u_search='/ü/';
			$search[]=$umlaut_u_search;
			$umlaut_y_search='/ÿ/';
			$search[]=$umlaut_y_search;
			$umlaut_A_search='/Ä/';
			$search[]=$umlaut_A_search;
			$umlaut_O_search='/O/';
			$search[]=$umlaut_O_search;
			$umlaut_U_search='/Ü/';
			$search[]=$umlaut_U_search;
			$umlaut_Y_search='/Ÿ/';
			$search[]=$umlaut_Y_search;

			$umlaut_a_replace='a';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace="o";
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace="u";
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace="y";
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace="A";
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace="Ö";
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace="U";
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace="Y";
			$replace[]=$umlaut_Y_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}

			# Reset the search and replace arrays.
			$search=array();
			$replace=array();

			# Twelfth, again
			$umlaut_a_search='/&a(uml|UML);/';
			$search[]=$umlaut_a_search;
			$umlaut_o_search='/&o(uml|UML);/';
			$search[]=$umlaut_o_search;
			$umlaut_u_search='/&u(uml|UML);/';
			$search[]=$umlaut_u_search;
			$umlaut_y_search='/&y(uml|UML);/';
			$search[]=$umlaut_y_search;
			$umlaut_A_search='/&A(uml|UML);/';
			$search[]=$umlaut_A_search;
			$umlaut_O_search='/&O(uml|UML);/';
			$search[]=$umlaut_O_search;
			$umlaut_U_search='/&U(uml|UML);/';
			$search[]=$umlaut_U_search;
			$umlaut_Y_search='/&Y(uml|UML);/';
			$search[]=$umlaut_Y_search;

			$umlaut_a_replace='a';
			$replace[]=$umlaut_a_replace;
			$umlaut_o_replace="o";
			$replace[]=$umlaut_o_replace;
			$umlaut_u_replace="u";
			$replace[]=$umlaut_u_replace;
			$umlaut_y_replace="y";
			$replace[]=$umlaut_y_replace;
			$umlaut_A_replace="A";
			$replace[]=$umlaut_A_replace;
			$umlaut_O_replace="O";
			$replace[]=$umlaut_O_replace;
			$umlaut_U_replace="U";
			$replace[]=$umlaut_U_replace;
			$umlaut_Y_replace="Y";
			$replace[]=$umlaut_Y_replace;

			$alt_term=preg_replace($search, $replace, $term);
			if(!in_array($alt_term, $alt_terms))
			{
				$alt_terms[]=$alt_term;
			}
		}
		# Loop through the alternate terms.
		foreach($alt_terms as $alt_term)
		{
			if(!empty($alt_term) && (strpos($exclude, $alt_term)===FALSE))
			{
				# Add the term to the output array ($out).
				if(!in_array($alt_term, $out))
				{
					$out[]=$alt_term;
				}
			}
		}
		return $out;
	} #==== End -- splitTerms
/*** ***/

	/**
	* change2Token
	*
	* Replaces any whitespace or comma in the string ($term) with a holder token.
	* Returns the transformed string.
	*
	* @param	$string (The string we're escaping)
	* @access	protected
	*/
	protected static function change2Token($term)
	{
		# Replace any whitespace ( ) with a holder token.
		$term=preg_replace("/(\s)/e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
		# Replace any comma (,) with a holder token.
		$term=preg_replace("/,/", "{COMMA}", $term);
		return $term;
	} #==== End -- change2Token

	/**
	 * emphasizeTerms
	 *
	 * Emphasize the search terms in the returned results search.
	 *
	 * @param		$terms (The terms to search for.)
	 * @param		$content (The returned content in which to emphasize terms.)
	 * @access	protected
	 */
	protected function emphasizeTerms($terms, $content)
	{
		# Create an array of all search terms.
		$a_terms=$this->splitTerms($terms);

		# Loop through all search terms and surround them with a <span> with a css class.
		foreach($a_terms as $term)
		{
			$r_terms[]='<span class="emphasize">'.$term.'</span>';
		}
		# Empty the $term variable
		$term=NULL;

		# Prepare the search terms for preg_replace.
		foreach($a_terms as $term)
		{
			$s_terms[]='/'.$term.'/i';
		}

		# pre_replace all search terms with "emphasized" terms.
		$content=preg_replace($s_terms, $r_terms, $content);

		return $content;
	} #==== End -- emphasizeTerms

	/**
	* searchEscapeMetaChars
	*
	* Escapes the string ($string) in-case some of the characters in the search term contain a MySQL regular expression meta-character.
	* Returns the escaped string.
	*
	* @param	$string (The string we're escaping)
	* @access	protected
	*/
	protected function escapeMetaChars($string)
	{
		# Insert a slash before each meta-character that MySQL uses.
		return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
	} #==== End -- searchEscapeMetaChars

	/**
	* convertTerms2RegEx
	*
	* Turns an array of search terms ($terms) into a list of regular expressions suitable for MYSQL.
	* Returns an array of regular expressions suitable for MYSQL.
	*
	* @param	$terms (The array of search terms)
	* @access	protected
	*/
	protected function convertTerms2RegEx($terms)
	{
		# Make certain that the passed variable is an array.
		$terms=(array)$terms;
		# Create a new array for our output.
		$out=array();
		# Loop through the search terms.
		foreach($terms as $term)
		{
			# Using the escapeMetaChars method, escape the search term and add it to our output array ($out).
			$out[]=addslashes($this->escapeMetaChars($term));
			//$out[]='[[:<:]]'.addslashes($this->escapeMetaChars($term)).'[[:>:]]';
			//$out[]='%'.addslashes($this->escapeMetaChars($term)).'%';
		}
		return $out;
	} #==== End -- convertTerms2RegEx

/*** NEEDS FUNCTIONALITY ***/
	/**
	* convertChars2Entities
	*
	* Converts any special characters to html entities.
	*
	* @param	$terms (The array of search terms)
	* @access	protected
	*/
	protected function convertChars2Entities($terms)
	{
		return $terms;
	} #==== End -- convertChars2Entities
/*** ***/

	/**
	* prepareWhere
	*
	* Builds and returns the "where" portion of the search query.
	*
	* @param	$terms (The term we're searching for.)
	* @param	$table (The table we're searching in.)
	* @param	$fields (The fields we're searching in.)
	* @param	$id (The name of the id field in the table we are searching.)
	* @param	$filter (Fields and or terms we would like exluded.)
	* @access	protected
	*/
	protected function prepareWhere($terms, $fields, $filter=NULL)
	{
		# Set the Database instance to a variable.
		$db=DB::get_instance();

		$terms=$this->splitTerms($terms);
		$terms_db=$this->convertTerms2RegEx($terms);
		$result=array();
		if(!empty($filter))
		{
			# $filter="`Party` = 'yes' AND "
			$filter=$filter.' AND ';
		}

		$parts=array();
		/*foreach($terms_db as $term_db)
		{
			# $parts[]="`Username` RLIKE '$term_db'";
			foreach($fields as $field)
			{
				$parts[]='`'.$field.'` RLIKE '.$db->quote($term_db);
			}
		}
		$parts=implode(' OR ', $parts);*/
		$terms_db=implode('|', $terms_db);
			# $parts[]="`Username` RLIKE '$term_db'";
		foreach($fields as $field)
		{
			$parts[]='`'.$field.'` RLIKE '.$db->quote($terms_db);
		}
		$parts=implode(' OR ', $parts);

		return $filter.((!empty($parts)) ? '('.$parts.')' : '');
	} #==== End -- prepareWhere

	/*** End protected methods ***/

} # End Search class.