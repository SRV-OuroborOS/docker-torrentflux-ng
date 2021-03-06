<?php

/* $Id$ */

/*************************************************************
*  TorrentFlux PHP Torrent Manager
*  www.torrentflux.com
**************************************************************/
/*
	This file is part of TorrentFlux.

	TorrentFlux is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	TorrentFlux is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with TorrentFlux; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class SearchEngine extends SearchEngineBase
{

	function SearchEngine($cfg)
	{
		$this->mainURL = "www.t411.me";
		$this->altURL = "www.t411.me";
		$this->mainTitle = "T411";
		$this->engineName = "T411";

		$this->author = "CaRa";
		$this->version = "1.01-tfb";
		$this->updateURL = "http://127.0.0.1";

		$this->Initialize($cfg);
	}

	function populateMainCategories()
	{

	
	$this->mainCatalog["210"] = "Video";
	$this->mainCatalog["395"] = "Musique";
	$this->mainCatalog["624"] = "Jeux-Video";

	}

	//----------------------------------------------------------------
	// Function to Get Sub Categories
/*	function getSubCategories($mainGenre)
	{
		return $output;

	}*/

	//----------------------------------------------------------------
	// Function to Make the Request (overriding base)
	function makeRequest($request)
	{
		return parent::makeRequest($request, false);
	}

	//----------------------------------------------------------------
	// Function to get Latest..
	function getLatest()
	{
		$cat = tfb_getRequestVar('mainGenre');

		if (empty($cat)) $cat = tfb_getRequestVar('cat');

		$request = "/top/100/";

		if(!empty($cat))
		{
			if(strpos($request,"?"))
			{
				$request .= "&cat=".$cat;
			}
			else
			{
				$request .= "?cat=".$cat;
			}
		}

		if (!empty($this->pg))
		{
			if(strpos($request,"?"))
			{
				$request .= "&page=" . $this->pg;
			}
			else
			{
				$request .= "?page=" . $this->pg;
			}
		}

		if ($this->makeRequest($request,true))
		{
			if (strlen($this->htmlPage) > 0 )
			{
				return $this->parseResponse();
			}
			else
			{
				return 'Unable to Browse at this time.';
			}
		}
		else
		{
			return $this->msg;
		}
	}

	//----------------------------------------------------------------
	// Function to perform Search.
	function performSearch($searchTerm)
	{
		// create the request string.
		$searchTerm = str_replace(" ", "+", $searchTerm);
		$request = "/torrents/search/?search=".$searchTerm."&order=seeders&type=desc";

		if(!empty($cat))
		{
			$request .= "&cat=".$cat;
		}

		$incldead = tfb_getRequestVar('incldead');
		if (empty($incldead)) $incldead = "0";
		$request .= "&incldead=".$incldead;

		if (!empty($this->pg))
		{
			$request .= "&page=" . $this->pg;
		}


		if ($this->makeRequest($request,true))
		{
			return $this->parseResponse();
		}
		else
		{
			return $this->msg;
		}
	}

	//----------------------------------------------------------------
	// Override the base to show custom table header.
	// Function to setup the table header
	function tableHeader()
	{
		$output = "<table width=\"100%\" cellpadding=3 cellspacing=0 border=0>";

		$output .= "<br>\n";

		$output .= "<tr bgcolor=\"".$this->cfg["bgLight"]."\">";
		if ($needWait)
		{
			$output .= "  <td colspan=9 align=center>";
		}
		else
		{
			$output .= "  <td colspan=8 align=center>";
		}



		if (is_integer(strpos($this->htmlPage,"Déconnexion, ")))
		{
		  $userinfo = substr($this->htmlPage,strpos($this->htmlPage,"Déconnexion, ")+strlen("Déconnexion, "));
		  $userinfo = substr($userinfo,strpos($userinfo,"<br/>")+strlen("<br/>"));

	  // Comment the following line and uncomment the next two if you would like to have the arrows for active torrents.  Remember to download the arrow files and put them in your images directory
	  $userinfo = substr($userinfo,0,strpos($userinfo,"Active:"));
		  //$userinfo = substr($userinfo,0,strpos($userinfo,"</td>"));
	  //$userinfo = str_replace("../pic/", "images/", $userinfo);

		  $output .= $userinfo;
		}
		$output .= "</td></tr>";

		$output .= "<tr bgcolor=\"".$this->cfg["table_header_bg"]."\">";
		$output .= "  <td>&nbsp;</td>";
		$output .= "  <td><strong>Torrent Name</strong> &nbsp;(";

		$tmpURI = str_replace(array("?hideSeedless=yes","&hideSeedless=yes","?hideSeedless=no","&hideSeedless=no"),"",$_SERVER["REQUEST_URI"]);

		// Check to see if Question mark is there.
		if (strpos($tmpURI,'?'))
		{
			$tmpURI .= "&";
		}
		else
		{
			$tmpURI .= "?";
		}

		if($this->hideSeedless == "yes")
		{
			$output .= "<a href=\"". $tmpURI . "hideSeedless=no\">Show Seedless</a>";
		}
		else
		{
			$output .= "<a href=\"". $tmpURI . "hideSeedless=yes\">Hide Seedless</a>";
		}

		$output .= ")</td>";
		$output .= "  <td><strong>Category</strong></td>";
	$output .= "  <td align=center><strong>Life</strong></td>";
		$output .= "  <td align=center><strong>Size</strong></td>";
		$output .= "  <td><strong>Seeds</strong></td>";
		$output .= "  <td><strong>Peers</strong></td>";
		$output .= "  <td><strong>Snatched</strong></td>";
		$output .= "</tr>\n";

		return $output;
	}

	//----------------------------------------------------------------
	// Function to parse the response.
		function parseResponse($latest = true)
	{


		$thing = $this->htmlPage;

		if(strpos($thing,"Plop") > 0)
		{
			$tmpStr = substr($thing,strpos($thing,"takelogin"));
			$tmpStr = substr($tmpStr,strpos($tmpStr, ">")+1);
			$tmpStr2 = "<form method=\"post\" action=\"http://".$this->mainURL."/users/login\">";
			$tmpStr = substr($tmpStr,0,strpos($tmpStr,"</form>")+strlen("</form>"));
			$output = $tmpStr2.str_replace("src=\"","src=\"http://".$this->mainURL."/",$tmpStr)."</table>";

		}
		else
		{

			$output = $this->tableHeader();

			if(strpos($thing,"Error:") > 0)
			{
				$tmpStr = substr($thing,strpos($thing,"Error:")+strlen("Error:"));
				$tmpStr = substr($tmpStr,0,strpos($tmpStr,"</p>"));
				$this->msg = strip_tags($tmpStr);
				return $output . "<center>".$this->msg."</center><br>";
			}

			// We got a response so display it.
			// Chop the front end off.
			$thing = substr($thing,strpos($thing,"<th>Torrent</th>"));

			$thing = substr($thing,strpos($thing,"<tr>")+strlen("<tr>"));

			//$tmpList = substr($thing,0,strpos($thing,"</table>"));
			// ok so now we have the listing.
			$tmpListArr = explode("</tr>",$thing);

			$bg = $this->cfg["bgLight"];
			//var_export($tmpListArr);
			foreach($tmpListArr as $key =>$value)
			{

				$buildLine = true;
				if (strpos($value,"id="))
				{
					$ts = new T411($value);

					if (!empty($ts->torrentFile) && $buildLine) {

						$output .= trim($ts->BuildOutput($bg,$this->searchURL()));

						// ok switch colors.
						if ($bg == $this->cfg["bgLight"])
						{
							$bg = $this->cfg["bgDark"];
						}
						else
						{
							$bg = $this->cfg["bgLight"];
						}
					}

				}
			}

			// set thing to end of this table.
			$thing = substr($thing,strpos($thing,"</table>"));

			$output .= "</table>";

			// is there paging at the bottom?
			if (strpos($thing, "page=") != false)
			{
				// Yes, then lets grab it and display it!  ;)

				$pages = substr($thing,strpos($thing,"<p"));
				$pages = substr($pages,strpos($pages,">"));
				$pages = substr($pages,0,strpos($pages,"</p>"));

				$pages = str_replace("&nbsp; ",'',$pages);

				$tmpPageArr = explode("</a>",$pages);
				array_pop($tmpPageArr);

				$pagesout = '';
				foreach($tmpPageArr as $key => $value)
				{
					$value .= "</a> &nbsp;";
					$tmpVal = substr($value,strpos($value,"browse.php?"),strpos($value,">")-1);

					$pgNum = substr($tmpVal,strpos($tmpVal,"page=")+strlen("page="));
					$pagesout .= str_replace($tmpVal,"XXXURLXXX".$pgNum,$value);
				}

				$cat = tfb_getRequestVar('mainGenre');

				if(strpos($this->curRequest,"LATEST"))
				{
					if (!empty($cat))
					{
						$pages = str_replace("XXXURLXXX",$this->searchURL()."&LATEST=1&cat=".$cat."&page=",$pagesout);
					}
					else
					{
						$pages = str_replace("XXXURLXXX",$this->searchURL()."&LATEST=1&page=",$pagesout);
					}
				}
				else
				{
					if(!empty($cat))
					{
						$pages = str_replace("XXXURLXXX",$this->searchURL()."/torrents/search/?search=".$_REQUEST["searchterm"]."&cat=".$cat."&page=",$pagesout);

					}
					else
					{
						$pages = str_replace("XXXURLXXX",$this->searchURL()."/torrents/search/?search=".$_REQUEST["searchterm"]."&page=",$pagesout);
					}
				}
				// $pages = strip_tags($pages,"<a><b>");
				$output .= "<div align=center>".substr($pages,1)."</div>";
			}
		}
		return $output;
	}
}


// This is a worker class that takes in a row in a table and parses it.
class T411
{
	var $torrentName = "";
	var $torrentDisplayName = "";
	var $torrentFile = "";
	var $torrentSize = "";
	var $torrentLife = "";
	var $torrentStatus = "";
	var $MainId = "";
	var $MainCategory = "";
	var $fileCount = "";
	var $Seeds = "";
	var $Peers = "";

	var $needsWait = false;
	var $waitTime = "";

	var $Data = "";

	var $torrentRating = "";

	function T411( $htmlLine )
	{
		if (strlen($htmlLine) > 0)
		{
			$this->Data = $htmlLine;
			
			//recherche ID du torrent
			// preg_match("/<a href=\"/torrents/nfo/?id=(.*?)\" class=\"ajax nfo\"></a>/i",$htmlLine,$id_tab);
			// $id = $id_tab[1];
			// Cleanup any bugs in the HTML
			$htmlLine = preg_replace("#</td>\n</td>#i",'</td>',$htmlLine);

			// Chunck up the row into columns.
			$tmpListArr = explode("<td",$htmlLine);

			$tmpStr = substr($tmpListArr["1"],strpos($tmpListArr["1"],"alt=\"")+strlen("alt=\"")); // MainCategory
			$this->MainCategory = substr($tmpStr,0,strpos($tmpStr,"\""));

			$tmpStr = substr($tmpListArr["1"],strpos($tmpListArr["1"],"cat=")+strlen("cat=")); // Main Id
			$this->MainId = substr($tmpStr,0,strpos($tmpStr,"\""));

			$tmpStr = substr($tmpListArr["2"],strpos($tmpListArr["2"],"title=\"")+strlen("title=\""));
			$this->torrentName = substr($tmpStr,0,strpos($tmpStr,"\""));

			$tmpStr = substr($tmpListArr["3"],strpos($tmpListArr["3"],"href=\"/torrents/nfo/?id=")+strlen("href=\"/torrents/nfo/?id="));
			$this->torrentFile = "http://www.T411.me/torrents/download/?id=".substr($tmpStr,0,strpos($tmpStr,"\""));

			$this->fileCount = $this->cleanLine("<td ".$tmpListArr["4"]."</td>");  // File Count

			$this->torrentLife = "&nbsp;&nbsp;".$this->cleanLine("<td ".$tmpListArr["5"]."</td>");

			$this->torrentSize = "&nbsp;&nbsp;".$this->cleanLine("<td ".$tmpListArr["6"]."</td>");  // Size of File

			$this->torrentStatus = $this->cleanLine(str_replace("<br>"," ","<td ".$tmpListArr["7"]."</td>")); // Snatched

			$this->Seeds = $this->cleanLine("<td ".$tmpListArr["8"]."</td>");  // Seeds
			$this->Peers = $this->cleanLine("<td ".$tmpListArr["9"]."</td>");  // Leech

			$this->torrentDisplayName = $this->torrentName;
			if(strlen($this->torrentDisplayName) > 50)
			{
				$this->torrentDisplayName = substr(str_replace("&nbsp;", " ", $this->torrentDisplayName),0,50)."...";
			}
		}

	}

	function cleanLine($stringIn,$tags='')
	{
		if(empty($tags))
			return trim(str_replace(array("&nbsp;","&nbsp")," ",strip_tags($stringIn)));
		else
			return trim(str_replace(array("&nbsp;","&nbsp")," ",strip_tags($stringIn,$tags)));
	}

	//----------------------------------------------------------------
	// Function to build output for the table.
	function BuildOutput($bg, $searchURL = '')
	{
		$output = "<tr>\n";
		$output .= "	<td width=16 bgcolor=\"".$bg."\"><a href=\"dispatcher.php?action=urlUpload&type=torrent&url=".$this->torrentFile."\"><img src=\"".getImagesPath()."download_owner.gif\" width=\"16\" height=\"16\" title=\"".$this->torrentName."\" border=0></a></td>\n";
		$output .= "	<td bgcolor=\"".$bg."\"><a href=\"dispatcher.php?action=urlUpload&type=torrent&url=".$this->torrentFile."\" title=\"".$this->torrentName."\">".$this->torrentDisplayName."</a></td>\n";

		if (strlen($this->MainCategory) > 1){
			$genre = "<a href=\"".$searchURL."&mainGenre=".$this->MainId."\">".$this->MainCategory."</a>";
		}else{
			$genre = "";
		}

		$output .= "	<td bgcolor=\"".$bg."\">". $genre ."</td>\n";

		$output .= "	<td bgcolor=\"".$bg."\" align=right>".$this->torrentLife."</td>\n";
		$output .= "	<td bgcolor=\"".$bg."\" align=right>".$this->torrentSize."</td>\n";
		$output .= "	<td bgcolor=\"".$bg."\" align=center>".$this->Seeds."</td>\n";
		$output .= "	<td bgcolor=\"".$bg."\" align=center>".$this->Peers."</td>\n";
		$output .= "	<td bgcolor=\"".$bg."\" align=center>".$this->torrentStatus."</td>\n";
		$output .= "</tr>\n";

		return $output;

	}
}

?>