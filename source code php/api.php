<?php

include 'simple_html_dom.php';

error_reporting(0);

/********************* URL DRAKOR-ID ***************************/
/***************************************************************/
$main_url_drakor_id = 'http://91.230.121.37/';
/***************************************************************/
/***************************************************************/

function ConnectionDatabase() {
	/**
	 * using mysqli_connect for database connection
	 */
	$databaseHost 		= 'localhost';
	$databaseName 		= 'botdrakorid';
	$databaseUsername 	= 'root';
	$databasePassword 	= '';

	$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);
	mysqli_set_charset($mysqli, "utf8"); 
	if (mysqli_connect_errno()){
		return FALSE;
	}
	return $mysqli;
}

if ( ! function_exists( 'MyCurl' ) ) {
    function MyCurl($url) {

    	$items = Array('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13');
        $user_agent = $items[array_rand($items)];
        
        $curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		//curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		   "Connection: keep-alive",
		   "Upgrade-Insecure-Requests: 1",
		   "User-Agent: " . $user_agent,
		   "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
		   "Referer: http://91.230.121.37/",
		   "Accept-Language: en-US,en;q=0.9",
		   "Cookie: HstCfa2915606=1625515655924; HstCla2915606=1625515655924; HstCmu2915606=1625515655924; HstPn2915606=1; HstPt2915606=1; HstCnv2915606=1; HstCns2915606=1; _ga=GA1.1.1114506183.1625515656; _gid=GA1.1.179122061.1625515656; _gat_gtag_UA_17650858_2=1; __dtsu=51A01625515656B1A646F8CCEE1DB9D1",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl); 

        return $resp;
    }
}

function CheckTableExists(){
	// Connection Database
	if(!$mysqli = ConnectionDatabase()) {
	    die('Failed to connect to MySQL');
	}

	$table_main = 'main';
	$result_check_main = $mysqli->query("SHOW TABLES LIKE '".$table_main."'");
	if(!$result_check_main->num_rows > 0) {
	    $create_sql_main = "CREATE TABLE ".$table_main." (
		                    id bigint(20) NOT NULL AUTO_INCREMENT,
		                    slug varchar(200) NOT NULL,
		                    datepost varchar(200) NOT NULL,
							image varchar(500) NOT NULL,
							status varchar(10) NOT NULL,
							UNIQUE KEY id (id)
		                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin";

        if (!$mysqli->query($create_sql_main) === TRUE) {
			echo('Create table main gagal, Silakan cek kembali setting database anda');
		}
	}

	$table_post = 'post';
	$result_check_post = $mysqli->query("SHOW TABLES LIKE '".$table_post."'");
	if(!$result_check_post->num_rows > 0) {
	    $create_sql_post = "CREATE TABLE ".$table_post." (
		                    id bigint(20) NOT NULL AUTO_INCREMENT,
		                    slug_id bigint(20) NOT NULL,
							entry_title TEXT(1000) NOT NULL,
							gmr_movie_rated varchar(200) NOT NULL,
							gmr_movie_genre varchar(200) NOT NULL,
							gmr_movie_quality varchar(200) NOT NULL,
							gmr_movie_year varchar(200) NOT NULL,
							gmr_movie_duration varchar(200) NOT NULL,
							gmr_movie_view varchar(200) NOT NULL,
							gmr_meta_rating_count varchar(200) NOT NULL,
							gmr_meta_rating_value varchar(200) NOT NULL,
							description TEXT(6000) NOT NULL,
							gmr_moviedata_tagline varchar(1000) NOT NULL,
							gmr_moviedata_content_ocation varchar(200) NOT NULL,
							gmr_moviedata_date_created varchar(200) NOT NULL,
							gmr_moviedata_in_language varchar(200) NOT NULL,
							gmr_moviedata_director varchar(2000) NOT NULL,
							gmr_moviedata_actors varchar(4000) NOT NULL,
							gmr_embed_responsive varchar(2000) NOT NULL,
							gmr_download_wrap varchar(3000) NOT NULL,
							UNIQUE KEY id (id)
		                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin";

        if (!$mysqli->query($create_sql_post) === TRUE) {
			echo('Create table post gagal, Silakan cek kembali setting database anda');
		} /*else {
			echo $mysqli->error;
		}*/
	}

	// Close Connection Database
	$mysqli->close();
}

function CheckDB($mysqli, $slug) {

	$query= "SELECT * FROM main WHERE slug LIKE '{$slug}' LIMIT 1"; 

	$res = mysqli_query($mysqli, $query); 
	if (mysqli_num_rows($res) > 0) {
		return true;
	} else {
		return false;
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['main'])) {

    	if ($_GET['main'] === 'start') {

    		echo('<a href="?main=get_url&page=1">Scrapper URL</a> &ensp; <a href="?main=url_data&page=1">Data URL</a> &ensp; <a href="?main=auto_get_content&id=0" target="_blank">Auto Get Content</a>');

    	} else if ($_GET['main'] === 'get_url') {

    		if (isset($_GET['page'])) {

    			if(!$mysqli = ConnectionDatabase()) {
				    die('Failed to connect to MySQL');
				}
    			
    			$html = str_get_html(MyCurl($main_url_drakor_id.'post-sitemap'.$_GET['page'].'.xml'));
	            if ($html !== null) {

	                $url = $html->find('url', 0);
	                if ($url !== null) {

	                    $no = 1;
	                    foreach ($html->find('url') as $key) {
	                        
	                        $loc = $key->find('loc', 0);
	                        if ($loc !== null) {
	                            $slug = basename($loc->plaintext);
	                        } else {
	                            $slug = '';
	                        }

	                        $datelastmod = $key->find('lastmod', 0);
	                        if ($datelastmod !== null) {
	                            $datepost = $datelastmod->plaintext;
	                        } else {
	                            $datepost = '';
	                        }

	                        $imageloc = $key->find('image:image image:loc', 0);
	                        if ($imageloc !== null) {
	                        	$items_head = Array('//i3.wp.com/', '//i2.wp.com/', '//i1.wp.com/');
								$head_ = $items_head[array_rand($items_head)];
	                            $image = $imageloc->plaintext;
	                            if (strpos($image, 'http:') !== false) {
	                            	$image = str_replace('http://', $head_, $image);
	                            } else if (strpos($image, 'https:') !== false) {
	                            	$image = str_replace('https://', $head_, $image);
	                            }
	                        } else {
	                            $image = '//upload.wikimedia.org/wikipedia/commons/thumb/1/16/No_image_available_450_x_600.svg/450px-No_image_available_450_x_600.svg.png';
	                        }

	                        if (!empty($slug) && $slug !== '' /*&& !empty($image) && $image !== ''*/) {
	                            if (CheckDB($mysqli, $slug)) {
	                                $msg____['msg'] = $slug . ' ke ' . $no . ' sudah ada';
	                            } else {
	                                $sql = "INSERT INTO main(id,slug,datepost,image,status) VALUES(NULL,'$slug','$datepost','$image','pending')";
			                    	if ($mysqli->query($sql) === TRUE) {
			                    		$msg_____['msg'] = $slug . ' ke ' . $no . ' berhasil disimpan';
			                    	} else {
			                            $msg_____['msg'] = $slug . ' ke ' . $no . ' gagal disimpan';
			                        }

	                                $msg____['msg'] = $msg_____;
	                            }

	                            $msg___['msg'] = $msg____;
	                        } else {
	                            $msg___['msg'] = '(!empty($slug) && $slug !==  && !empty($image) && $image !== ) ke ' . $no;
	                        }

	                        $msg__[] = $msg___;
	                        $no++;
	                    }

	            		$msg_['msg'] = $msg__;
	            	} else {
	            		$msg_['msg'] = 'url == null';
	            	}

	            	$msg['msg'] = $msg_;

	                // clean up memory
	                $html->clear();
	                unset($html);
	            } else {
	                $msg['msg'] = 'html null';
	            }

	            $page_new = $_GET['page'] + 1;
	            echo('<a href="?main=get_url&page='.$page_new.'">Next Scrapper URL</a> &ensp; <a href="?main=url_data&page=1">Data URL</a> &ensp; <a href="?main=auto_get_content&id=0" target="_blank">Auto Get Content</a>');
	            echo('<p>');

	            $data_result[] = $msg;
            	echo(json_encode($data_result));

            	// Close Connection Database
				$mysqli->close();

    		} else {
    			echo "Nothing yet";
    		}

    	} else if ($_GET['main'] === 'url_data') {

    		if (isset($_GET['page'])) {

    			echo('<a href="?main=get_url&page=1">Scrapper URL</a> &ensp; <a href="?main=url_data&page=1">Data URL</a> &ensp; <a href="?main=auto_get_content&id=0" target="_blank">Auto Get Content</a>');
	            echo('<p>');
	            echo('<p>');

    			// Connection Database
				if(!$mysqli = ConnectionDatabase()) {
				    die('Failed to connect to MySQL');
				}

				?>
				<style type="text/css">
					@import "bootstrap.min.css";
				</style>
				<table class="table table-striped table-bordered">
				<thead>
				<tr>
				<th style='width:2%;'>ID</th>
				<th style='width:20%;'>Slug</th>
				<th style='width:10%;'>Date</th>
				<th style='width:5%;'>Status</th>
				<th style='width:7%;'>Opsi</th>
				<th style='width:8%;'>Open Web</th>
				</tr>
				</thead>
				<tbody>
				<?php

				$page = $_GET['page'];

				$total_records_per_page = 10;
				$offset = ($page-1) * $total_records_per_page;
				$previous_page = $page - 1;
				$next_page = $page + 1;
				$adjacents = "2";

				$result_count = mysqli_query($mysqli, "SELECT COUNT(*) As total_records FROM `main`");
				$total_records = mysqli_fetch_array($result_count);
				$total_records = $total_records['total_records'];
				$total_no_of_pages = ceil($total_records / $total_records_per_page);
				$second_last = $total_no_of_pages - 1;

				$result = mysqli_query($mysqli, "SELECT * FROM `main` LIMIT $offset, $total_records_per_page");
				while($row = mysqli_fetch_array($result)){
					$id   = $row['id'];
					$slug = $row['slug'];
			    	echo "<tr>
			    	<td>".$row['id']."</td>
					<td>".$row['slug']."</td>
					<td>".$row['datepost']."</td>
					<td>".$row['status']."</td>";
					if ($row['status']  === 'pending') {
						echo("<td><a href='?main=auto_get_content&id=$id' target='_blank'>Post</a></td>");
					} elseif ($row['status']  === 'publish') {
						echo("<td><a href='?main=auto_get_content&id=$id&update' target='_blank'>Update</a></td>");
					}
					echo("<td><a href='$main_url_drakor_id$slug' target='_blank'>Open web</a></td>");
					echo "</tr>";
			    }

				// Close Connection Database
				$mysqli->close();

				?>
				</tbody>
				</table>

				<div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
					<strong>Page <?php echo $page." of ".$total_no_of_pages; ?></strong>
				</div>
				<ul class="pagination">
					<?php // if($page > 1){ echo "<li><a href='?page_data=1'>First Page</a></li>"; } ?>
				    
					<li <?php if($page <= 1){ echo "class='disabled'"; } ?>>
						<a <?php if($page > 1){ echo "href='?main=url_data&page=$previous_page'"; } ?>>Previous</a>
					</li>
				       
				    <?php 
					if($total_no_of_pages <= 10){  	 
						for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
							if ($counter == $page) {
						   		echo "<li class='active'><a>$counter</a></li>";	
							}else{
				          		echo "<li><a href='?main=url_data&page=$counter'>$counter</a></li>";
							}
				        }
					}elseif($total_no_of_pages > 10){
						if($page <= 4) {
							for ($counter = 1; $counter < 8; $counter++){		 
								if ($counter == $page) {
			   						echo "<li class='active'><a>$counter</a></li>";	
								}else{
	           						echo "<li><a href='?main=url_data&page=$counter'>$counter</a></li>";
								}
	        				}
	        				echo "<li><a>...</a></li>";
							echo "<li><a href='?main=url_data&page=$second_last'>$second_last</a></li>";
							echo "<li><a href='?main=url_data&page=$total_no_of_pages'>$total_no_of_pages</a></li>";
						}elseif($page > 4 && $page < $total_no_of_pages - 4) {
							echo "<li><a href='?main=url_data&page=1'>1</a></li>";
							echo "<li><a href='?main=url_data&page=2'>2</a></li>";
					        echo "<li><a>...</a></li>";
					        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {			
	           					if ($counter == $page) {
			   						echo "<li class='active'><a>$counter</a></li>";	
								}else{
	           						echo "<li><a href='?main=url_data&page=$counter'>$counter</a></li>";
								}
							}
							echo "<li><a>...</a></li>";
							echo "<li><a href='?main=url_data&page=$second_last'>$second_last</a></li>";
							echo "<li><a href='?main=url_data&page=$total_no_of_pages'>$total_no_of_pages</a></li>"; 
						}else{
					        echo "<li><a href='?main=url_data&page=1'>1</a></li>";
							echo "<li><a href='?main=url_data&page=2'>2</a></li>";
					        echo "<li><a>...</a></li>";

					        for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
					          	if ($counter == $page) {
							   		echo "<li class='active'><a>$counter</a></li>";	
								}else{
					           		echo "<li><a href='?main=url_data&page=$counter'>$counter</a></li>";
								}                   
					        }
					    }
					}
					
					?>
					<li <?php if($page >= $total_no_of_pages){ echo "class='disabled'"; } ?>>
						<a <?php if($page < $total_no_of_pages) { echo "href='?main=url_data&page=$next_page'"; } ?>>Next</a>
					</li>
	    			<?php if($page < $total_no_of_pages){
						echo "<li><a href='?main=url_data&page=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
					} ?>
				
				</ul>
				<?php
    			
    		} else {
    			echo "Nothing yet";
    		}

    	} else if ($_GET['main'] === 'auto_get_content') {

    		if (isset($_GET['id'])) {
    			
    			// Connection Database
				if(!$mysqli = ConnectionDatabase()) {
				    die('Failed to connect to MySQL');
				}

				$slug_id = '';
				$pull_left_image = '';
				$entry_title = '';
				$gmr_movie_rated = '';
				$gmr_movie_genre = '';
				$gmr_movie_quality = '';
				$gmr_movie_year = '';
				$gmr_movie_duration = '';
				$gmr_movie_view = '';
				$gmr_meta_rating_ = '';
				$gmr_meta_rating_count = '';
				$gmr_meta_rating_value = '';
				$description = '';
				$gmr_moviedata_tagline = '';
				$gmr_moviedata_content_ocation = '';
				$gmr_moviedata_date_created = '';
				$gmr_moviedata_in_language = '';
				$gmr_moviedata_anggaran = '';
				$gmr_moviedata_pendapatan = '';
				$gmr_moviedata_director = '';
				$gmr_moviedata_actors = '';

				if ($_GET['id'] !== '0') {
					$result = mysqli_fetch_array($mysqli->query("SELECT * FROM main WHERE id='{$_GET['id']}' LIMIT 1"));
				} else {
					$result = mysqli_fetch_array($mysqli->query("SELECT * FROM main WHERE status='pending' LIMIT 1"));
				}

				if (empty($result)){
					exit('No Database');
				}

				$slug_id = $result['id'];
				$slug = $result['slug'];

				$html_ = str_get_html(MyCurl($main_url_drakor_id.$slug.'/'));
				//$html_ = str_get_html(MyCurl('http://muvipro.com/2021/06/24/sister-laws-job/'));
				//$html_ = str_get_html(MyCurl('http://91.230.121.37/sister-laws-job/'));
            	if ($html_ !== null) {

            		$article = $html_->find('article', 0);
	                if ($article !== null) {
	                    $post_id = str_replace('post-', '', $article->id);
	                } else {
	                    echo(json_encode(array('msg' => 'html_->find(article, 0) null')));
	                    exit();
	                }

	                $gmr_movie_data_top = $html_->find('.gmr-movie-data-top', 0);
	                if ($gmr_movie_data_top !== null) {

	                	$post_title = '';
	                	if ($title__ = $gmr_movie_data_top->find('h1', 0)) {
	                        $post_title = $title__->plaintext;
	                    } else {
	                        echo(json_encode(array('msg' => 'post_title null')));
	                        exit();
	                    }

	                    $post_rated = '';
	                    if ($rated__ = $gmr_movie_data_top->find('.gmr-movie-rated', 0)) {
	                        $post_rated = $rated__->plaintext;
	                    }

	                    $post_genre = [];
	                    if ($genre__ = $gmr_movie_data_top->find('.gmr-movie-genre', 0)) {
	                        foreach ($genre__->find('a') as $key) {
	                            $post_genre[] = $key->plaintext;
	                        }
	                    }

	                    $post_quality = '';
	                    if ($quality__ = $gmr_movie_data_top->find('.gmr-movie-quality a', 0)) {
	                        $post_quality = $quality__->plaintext;
	                    }

	                    $post_year = '';
	                    if ($year__ = $gmr_movie_data_top->find('.gmr-movie-genre', 1)) {
	                        $post_year = $year__->find('a', 0)->plaintext;
	                    }

	                    $post_duration = '';
	                    if ($runtime__ = $gmr_movie_data_top->find('.gmr-movie-runtime', 0)) {
	                        $runtime_ = $runtime__->plaintext;
	                        preg_match("/([0-9]+)/", $runtime_, $matches);
	                        $post_duration = $matches[1];
	                    }

	                    $post_view = '';
	                    if ($view_data = $gmr_movie_data_top->find('.gmr-movie-view', 0)) {
	                        $view_data_ku = $view_data->plaintext;
	                        preg_match_all('!\d+!', $view_data_ku, $matchess);
	                        if (isset($matchess[0][1])) {
	                        	$post_view = $matchess[0][0] . $matchess[0][1];
	                        } else {
	                        	$post_view = $matchess[0][0];
	                        }
	                    } else {
	                    	$post_view = rand(99, 1000);
	                    }

	                } else {
	                    echo(json_encode(array('msg' => '$gmr_movie_data_top null')));
	                    exit();
	                }

	                $gmr_meta_rating = $html_->find('.gmr-meta-rating', 0);
	                $post_rating_count = '';
	                $post_rating_value = '';
	                if ($gmr_meta_rating !== null) {
	                    $post_rating_count = $gmr_meta_rating->find('[itemprop=ratingCount]', 0)->plaintext;
	                    $rating_value__ = $gmr_meta_rating->find('[itemprop=ratingValue]', 0)->plaintext;
	                    $post_rating_value = str_replace(',', '.', $rating_value__);
	                }

	                $entry_content_entry_content_single = $html_->find('.entry-content.entry-content-single', 0);
	                if ($entry_content_entry_content_single !== null) {
	                    
	                    $post_description = '';
	                    if ($description__ = $entry_content_entry_content_single->find('p', 0)) {
	                        $post_description = $description__->plaintext;
	                    }

	                    $post_tagline = $post_title;

	                    $post_country = [];
	                    if ($country__ = $entry_content_entry_content_single->find('[itemprop=contentLocation]', 0)) {
	                        foreach ($country__->find('a') as $key) {
	                            $post_country[] = $key->plaintext;
	                        }
	                    }

	                    $post_release = '';
	                    if ($release__ = $entry_content_entry_content_single->find('[itemprop=dateCreated]', 0)) {
	                        $post_release = $release__->plaintext;
	                    }

	                    $post_language = '';
	                    if ($language__ = $entry_content_entry_content_single->find('[property=inLanguage]', 0)) {
	                        $post_language = $language__->plaintext;
	                    }

	                    $post_director = [];
	                    foreach ($entry_content_entry_content_single->find('[itemprop=director]') as $key) {
	                        $post_director[] = $key->find('span a', 0)->plaintext;
	                    }

	                    $post_actors = [];
	                    foreach ($entry_content_entry_content_single->find('[itemprop=actors]') as $key) {
	                        $post_actors[] = $key->find('span a', 0)->plaintext;
	                    }
	                } else {
	                    echo(json_encode(array('msg' => '$entry_content_entry_content_single null')));
	                    exit();
	                }

	                $post_player = '';
	                $gmr_embed_responsive = $html_->find('.gmr-embed-responsive', 0);
	                if ($gmr_embed_responsive !== null) {
	                	$iframe = $gmr_embed_responsive->find('iframe', 0);
	                	if ($iframe !== null) {
	                		$player___ = $iframe->src;
	                		if (strpos($player___, 'gdriveplayer') !== false) {
			                    $query_str = parse_url($player___, PHP_URL_QUERY);
			                    parse_str($query_str, $query_params);
			                    $player__ = $query_params['data'];
			                    $post_player = urldecode(urldecode($player__));
			                }
	                	} else {
	                		$post_player = '//databasegdriveplayer.co/player.php?imdb=00000000000099999';
	                	}
	                }

	                $post_download = [];
	                if ($list_inline_gmr_download_list_clearfix = $html_->find('.list-inline.gmr-download-list.clearfix', 0)) {
	                    foreach ($list_inline_gmr_download_list_clearfix->find('li') as $key) {
	                        $download_link = $key->find('a', 0)->href;
	                        $download_title = $key->plaintext;

	                        if (strpos($download_link, "'") !== false) {
	                        	$download_link = str_replace("'", "", $download_link);
	                        }
	                        $post_download[] = array($download_title, $download_link);
	                    }
	                }

	                // clean up memory
	                $html_->clear();
	                unset($html_);
	            } else {
	                echo(json_encode(array('msg' => 'html_ = file_get_html '. $url.''.$slug.'/')));
	                exit();
	            } 

	            $post_genre_json = json_encode($post_genre);
	            $post_country_json = json_encode($post_country);
	            $post_director_json = json_encode($post_director);
	            $post_actors_json = json_encode($post_actors);
	            $post_download_json = json_encode($post_download);

	            $query_check = "SELECT * FROM post WHERE slug_id LIKE '{$slug_id}' LIMIT 1"; 

				$res_check_new = mysqli_query($mysqli, $query_check);

	            if (isset($_GET['update']) || mysqli_num_rows($res_check_new) > 0) {
	            	$sql = "UPDATE post SET gmr_movie_view = '{$post_view}', gmr_embed_responsive = '{$post_player}' WHERE slug_id = '{$slug_id}'";

	            	$message_mysqli = 'update data';
	            } else {
	            	$sql = "INSERT INTO post(id,slug_id,entry_title,gmr_movie_rated,gmr_movie_genre,gmr_movie_quality,gmr_movie_year,gmr_movie_duration,gmr_movie_view,gmr_meta_rating_count,gmr_meta_rating_value,description,gmr_moviedata_tagline,gmr_moviedata_content_ocation,gmr_moviedata_date_created,gmr_moviedata_in_language,gmr_moviedata_director,gmr_moviedata_actors,gmr_embed_responsive,gmr_download_wrap) VALUES(NULL,'{$slug_id}','$post_title','$post_rated','$post_genre_json','$post_quality','$post_year','$post_duration','$post_view','$post_rating_count','$post_rating_value','$post_description','$post_tagline','$post_country_json','$post_release','$post_language','$post_director_json','$post_actors_json','$post_player','$post_download_json')";

	            	$message_mysqli = 'add data';
	            }

	            if ($mysqli->query($sql) === TRUE) {

	            	if (!isset($_GET['update'])) {
	            		$sql_ = "UPDATE main SET status = 'publish' WHERE id = '{$slug_id}'";

						if ($mysqli->query($sql_) === TRUE) {
							echo('table main berhasil di update ');
						} else {
					        echo('table main gagal di update ');
					    }
	            	} else {
	            		echo($slug.' berhasil '.$message_mysqli);
	            	}

	            	echo('- '.$slug.' berhasil '.$message_mysqli);

	            	if ($_GET['id'] === '0') {
	            		echo('<br/><br/>Auto post running, please please don\'t click anything.<br/>To stop please reload the page.<br/><br/><div id="second"></div><a id="auto-click" href="?main=auto_get_content&id=0"></a>');
	            		echo('<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		            		<script type="text/javascript">
				                jQuery(document).ready(function($){
				                    var timer;
	                    			var second;

	                    			second = 35;
	                                clearTimeout(timer);
								  	HitungMundur();

								  	function HitungMundur() {
										timer = setTimeout(HitungMundur, 1000);
										if (second <= 0) {
											$("#second").html("Working...");
										} else {
											$("#second").html("Auto post will start in " + second + " detik.");
										}
										second --;
									}

									setTimeout(function() {
								    	document.getElementById("auto-click").click();
								  	}, 35000);
				                });
				            </script>');
	            	}

				} else {
			        echo($slug.' gagal '.$message_mysqli);
			    }	            			

				// Close Connection Database
				$mysqli->close();

    		} else {
    			echo "Nothing yet";
    		}

    	} else if ($_GET['main'] === 'api') {

    		if (isset($_GET['page'])) {

    			header('Content-Type: application/json; charset=utf-8');
    			
    			// Connection Database
				if(!$mysqli = ConnectionDatabase()) {
				    die('Failed to connect to MySQL');
				}

				$page = $_GET['page'];

				// set the number of items to display per page
				$items_per_page = 3;

				// build query
				$offset = ($page - 1) * $items_per_page;

				$sql_api = "SELECT m.*, p.* FROM main m, post p WHERE m.id = p.slug_id LIMIT " . $offset . "," . $items_per_page;

				$result_api = $mysqli->query($sql_api);
				$array = array();
				if ($result_api) {
					while ($row  = mysqli_fetch_assoc($result_api)){
		                $array[] = $row; 
		            }
				}
		        echo json_encode($array);

				// Close Connection Database
				$mysqli->close();

    		} else {
    			echo "Use param &page=1";
    		}

    	}

    } else {

    	CheckTableExists();

    	echo("<a href='?main=start'>Let's start</a>");

    	echo('<br/><br/>Untuk API gunakan methog GET, example "https://namedomain.com/bot.php?main=api&page=1"');
   
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
	header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');

	if (isset($_POST['main'])) {

		// Connection Database
		if(!$mysqli = ConnectionDatabase()) {
		    die('Failed to connect to MySQL');
		}

		if ($_POST['main'] === 'in') {

			$id_slug = $_POST['idslug'];
			$data_body_html = $_POST['datahtml'];

			$html_ = str_get_html($data_body_html);
        	if ($html_ !== null) {

        		$article = $html_->find('article', 0);
                if ($article !== null) {
                    $post_id = str_replace('post-', '', $article->id);
                } else {
                	echo json_encode(array("code" => 0, "message" => "html_->find(article, 0) null"));
                    exit();
                }

                $gmr_movie_data_top = $html_->find('.gmr-movie-data-top', 0);
                if ($gmr_movie_data_top !== null) {

                	$post_title = '';
                	if ($title__ = $gmr_movie_data_top->find('h1', 0)) {
                        $post_title = $title__->plaintext;
                    } else {
                    	echo json_encode(array("code" => 0, "message" => "post_title null"));
                        exit();
                    }

                    $post_rated = '';
                    if ($rated__ = $gmr_movie_data_top->find('.gmr-movie-rated', 0)) {
                        $post_rated = $rated__->plaintext;
                    }

                    $post_genre = [];
                    if ($genre__ = $gmr_movie_data_top->find('.gmr-movie-genre', 0)) {
                        foreach ($genre__->find('a') as $key) {
                            $post_genre[] = $key->plaintext;
                        }
                    }

                    $post_quality = '';
                    if ($quality__ = $gmr_movie_data_top->find('.gmr-movie-quality a', 0)) {
                        $post_quality = $quality__->plaintext;
                    }

                    $post_year = '';
                    if ($year__ = $gmr_movie_data_top->find('.gmr-movie-genre', 1)) {
                        $post_year = $year__->find('a', 0)->plaintext;
                    }

                    $post_duration = '';
                    if ($runtime__ = $gmr_movie_data_top->find('.gmr-movie-runtime', 0)) {
                        $runtime_ = $runtime__->plaintext;
                        preg_match("/([0-9]+)/", $runtime_, $matches);
                        $post_duration = $matches[1];
                    }

                    $post_view = '';
                    if ($view_data = $gmr_movie_data_top->find('.gmr-movie-view', 0)) {
                        $view_data_ku = $view_data->plaintext;
                        preg_match_all('!\d+!', $view_data_ku, $matchess);
                        if (isset($matchess[0][1])) {
                        	$post_view = $matchess[0][0] . $matchess[0][1];
                        } else {
                        	$post_view = $matchess[0][0];
                        }
                    } else {
                    	$post_view = rand(99, 1000);
                    }

                } else {
                	echo json_encode(array("code" => 0, "message" => "gmr_movie_data_top null"));
                    exit();
                }

                $gmr_meta_rating = $html_->find('.gmr-meta-rating', 0);
                $post_rating_count = '';
                $post_rating_value = '';
                if ($gmr_meta_rating !== null) {
                    $post_rating_count = $gmr_meta_rating->find('[itemprop=ratingCount]', 0)->plaintext;
                    $rating_value__ = $gmr_meta_rating->find('[itemprop=ratingValue]', 0)->plaintext;
                    $post_rating_value = str_replace(',', '.', $rating_value__);
                }

                $entry_content_entry_content_single = $html_->find('.entry-content.entry-content-single', 0);
                if ($entry_content_entry_content_single !== null) {
                    
                    $post_description = '';
                    if ($description__ = $entry_content_entry_content_single->find('p', 0)) {
                        $post_description = $description__->plaintext;
                    }

                    $post_tagline = $post_title;

                    $post_country = [];
                    if ($country__ = $entry_content_entry_content_single->find('[itemprop=contentLocation]', 0)) {
                        foreach ($country__->find('a') as $key) {
                            $post_country[] = $key->plaintext;
                        }
                    }

                    $post_release = '';
                    if ($release__ = $entry_content_entry_content_single->find('[itemprop=dateCreated]', 0)) {
                        $post_release = $release__->plaintext;
                    }

                    $post_language = '';
                    if ($language__ = $entry_content_entry_content_single->find('[property=inLanguage]', 0)) {
                        $post_language = $language__->plaintext;
                    }

                    $post_director = [];
                    foreach ($entry_content_entry_content_single->find('[itemprop=director]') as $key) {
                        $post_director[] = $key->find('span a', 0)->plaintext;
                    }

                    $post_actors = [];
                    foreach ($entry_content_entry_content_single->find('[itemprop=actors]') as $key) {
                        $post_actors[] = $key->find('span a', 0)->plaintext;
                    }
                } else {
                	echo json_encode(array("code" => 0, "message" => "entry_content_entry_content_single null"));
                    exit();
                }

                $post_player = '';
                $gmr_embed_responsive = $html_->find('.gmr-embed-responsive', 0);
                if ($gmr_embed_responsive !== null) {
                	$iframe = $gmr_embed_responsive->find('iframe', 0);
                	if ($iframe !== null) {
                		$player___ = $iframe->src;
                		if (strpos($player___, 'gdriveplayer') !== false) {
		                    $query_str = parse_url($player___, PHP_URL_QUERY);
		                    parse_str($query_str, $query_params);
		                    $player__ = $query_params['data'];
		                    $post_player = urldecode(urldecode($player__));
		                }
                	} else {
                		$post_player = '//databasegdriveplayer.co/player.php?imdb=00000000000099999';
                	}
                }

                $post_download = [];
                if ($list_inline_gmr_download_list_clearfix = $html_->find('.list-inline.gmr-download-list.clearfix', 0)) {
                    foreach ($list_inline_gmr_download_list_clearfix->find('li') as $key) {
                        $download_link = $key->find('a', 0)->href;
                        $download_title = $key->plaintext;

                        if (strpos($download_link, "'") !== false) {
                        	$download_link = str_replace("'", "", $download_link);
                        }
                        $post_download[] = array($download_title, $download_link);
                    }
                }

                // clean up memory
                $html_->clear();
                unset($html_);
            } else {
                echo json_encode(array("code" => 0, "message" => "html_ = file_get_html " . $url. "" .$id_slug. "/"));
                exit();
            } 

            $post_genre_json = json_encode($post_genre);
            $post_country_json = json_encode($post_country);
            $post_director_json = json_encode($post_director);
            $post_actors_json = json_encode($post_actors);
            $post_download_json = json_encode($post_download);

            $query_check = "SELECT * FROM post WHERE slug_id LIKE '{$id_slug}' LIMIT 1"; 

			$res_check_new = mysqli_query($mysqli, $query_check);

            if (mysqli_num_rows($res_check_new) > 0) {
            	$sql = "UPDATE post SET gmr_movie_view = '{$post_view}', gmr_embed_responsive = '{$post_player}' WHERE slug_id = '{$id_slug}'";

            	$message_mysqli = 'update data';
            } else {
            	$sql = "INSERT INTO post(id,slug_id,entry_title,gmr_movie_rated,gmr_movie_genre,gmr_movie_quality,gmr_movie_year,gmr_movie_duration,gmr_movie_view,gmr_meta_rating_count,gmr_meta_rating_value,description,gmr_moviedata_tagline,gmr_moviedata_content_ocation,gmr_moviedata_date_created,gmr_moviedata_in_language,gmr_moviedata_director,gmr_moviedata_actors,gmr_embed_responsive,gmr_download_wrap) VALUES(NULL,'{$id_slug}','$post_title','$post_rated','$post_genre_json','$post_quality','$post_year','$post_duration','$post_view','$post_rating_count','$post_rating_value','$post_description','$post_tagline','$post_country_json','$post_release','$post_language','$post_director_json','$post_actors_json','$post_player','$post_download_json')";

            	$message_mysqli = 'add data';
            }

            if ($mysqli->query($sql) === TRUE) {

            	$sql_ = "UPDATE main SET status = 'publish' WHERE id = '{$id_slug}'";

				if ($mysqli->query($sql_) === TRUE) {
					$mainTableMessage = 'table main berhasil di update ';
				} else {
			        $mainTableMessage = 'table main gagal di update ';
			    }

            	echo json_encode(array("code" => 1, "result" => "" . $mainTableMessage . "- " .$id_slug. " berhasil " .$message_mysqli . ""));
			} else {

		        echo json_encode(array("code" => 0, "message" => "" . $id_slug." gagal " .$message_mysqli . ""));
		    }	            			

		} else if ($_POST['main'] === 'out') {

			$query= "SELECT * FROM main WHERE status LIKE '%pending' ORDER BY id LIMIT 2"; 

			$result = mysqli_query($mysqli, $query); 

			$array = array();
            while ($row  = mysqli_fetch_assoc($result)){
                $array[] = $row; 
            }

            if ($result) {
            	if (mysqli_num_rows($result) > 0) {
					echo json_encode(array("code" => 1, "result" => $array));
				} else {
					echo json_encode(array("code" => 0, "message" => "Data not found"));
				}
            } else {
            	echo json_encode(array("code" => 0, "message" => "erro mysqli"));
            }

		}

		// Close Connection Database
		$mysqli->close();

	}

}			