import java.io.*;
import java.sql.*;
import java.util.*;
import java.net.URL;

import it.sauronsoftware.feed4j.FeedParser;
import it.sauronsoftware.feed4j.bean.Feed;
import it.sauronsoftware.feed4j.bean.FeedHeader;
import it.sauronsoftware.feed4j.bean.FeedItem;

import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class ReaderUpdater {
	static String host = "localhost";						// -h
	static String port = "3306";							// -P
	static String database = "sarah";						// -d
	static String delay = "1200000";						// -D (20 min default)
	static String username = "douglas";						// -u
	static String password = "fargo";						// -p

	static boolean isVerbose = false;
	static boolean isDebug = false;
	
	public static void main (String[] args) {
		// read global sarah config (for database details), Do this before parsing arguments so arguments can override global config settings
		JSONParser jsonParser = new JSONParser();
        try {     
            Object obj = jsonParser.parse(new FileReader("../../../../config.json"));

            JSONObject jsonObject =  (JSONObject) obj;

            host = (String) jsonObject.get("DB_ADDRESS");
            port = (String) jsonObject.get("DB_PORT");
            database = (String) jsonObject.get("DB_NAME");
            username = (String) jsonObject.get("DB_USER");
            password = (String) jsonObject.get("DB_PASS");
        } catch (Exception e) {
            System.out.println ("ERROR: parsing global config.json");
        }

		// parse arguments
		for (int i = 0; i < args.length; i++) {
			String arg = args[i];
			String value = "";
			char key = ' ';
			
			if (arg.startsWith ("-") && arg.length() >= 2) {
				key = arg.charAt(1);
				
				// no value for these flags, so we dont want to define the value
				if ( !(arg.equals("-v") || arg.equals("-t")) ){
					value = args[++i];
				}
			}

			switch (key) {
				case 'h': 	host = value;
							break;
				case 'P':	port = value;
							break;
				case 'd':	database = value;
							break;
				case 'D':	delay = value;
							break;
				case 'u':	username = value;
							break;
				case 'p':	password = value;
							break;
				case 'v':	isVerbose = true;
							break;
				case 't':	isDebug = true;
							break;
				case '?':	showHelp();
							break;
				default:	System.out.println ("See -? for help");
			};
		}
		
		while (true) {
			java.util.Date date = new java.util.Date();
			long start_time = date.getTime();
			String start_timestamp = new Timestamp (start_time).toString();
		
			updateFeeds();
			
			date = new java.util.Date();
			long end_time = date.getTime();
			String end_timestamp = new Timestamp (end_time).toString();
			
			System.out.println ("                ----");
			System.out.println ("         Start time: " + start_timestamp);
			System.out.println ("           End time: " + end_timestamp);
			System.out.println ("         Total time: " + (end_time - start_time) );
			
			try {
				Thread.sleep (Integer.parseInt (delay));
			} catch (Exception e) { }
		}
	}
	
	private static void showHelp () {
		System.out.println ("Help");
	}
	
	private static void sqlError (Exception e) {
		e.printStackTrace();
		System.out.println ("Error: " + e.getMessage());
	}
	private static void sqlError (Exception e, String sql) {
		e.printStackTrace();
		System.out.println ("Error: " + e.getMessage());
		System.out.println ("  SQL: " + sql);
	}
	
	private static void updateFeeds () {
		String sql_count	= "";
		String sql_insert	= "";
		String sql = "";
		ResultSet rs;
		
		// stats
		int number_of_feeds = 0;
		int number_of_new_items = 0;
		int number_of_existing_items = 0;
		int number_of_items = 0;
		
		try {
			Class.forName("com.mysql.jdbc.Driver");
			Connection conn = DriverManager.getConnection("jdbc:mysql://" + host + ":" + port + "/" + database, username, password);
			Statement stmt = conn.createStatement();
			Statement stmt2 = conn.createStatement();
			
			if (!isVerbose) {
				System.out.println ("Running . . .");
			}
			
			rs = stmt.executeQuery ("SELECT COUNT(*) FROM `reader_feeds` WHERE `isDisabled` != 1;");
			rs.next();
			int feeds_count = rs.getInt("COUNT(*)");
			
			sql = "SELECT * FROM `reader_feeds` WHERE `isDisabled` != 1;";
			rs = stmt.executeQuery (sql);
			
			String[] userid = new String [feeds_count];
			String[] feedid = new String [feeds_count];
			String[] feed_name = new String [feeds_count];
			String[] label = new String [feeds_count];
			String[] rss = new String [feeds_count];
			
			while (rs.next()) {
				userid[number_of_feeds] = rs.getString("USER_ID");
				feedid[number_of_feeds] = rs.getString("FEED_ID");
				feed_name[number_of_feeds] = rs.getString("name");
				label[number_of_feeds] = rs.getString("label");
				rss[number_of_feeds] = rs.getString("rss");
				
				number_of_feeds++;
			}
			
			for (int feed_index = 0; feed_index <= feeds_count - 1; feed_index++) {
				try {
					if (isVerbose) {
						System.out.println ("u" + userid[feed_index] + ":f" + feedid[feed_index] + ":" + feed_name[feed_index]);
					}
					
					Feed feed = FeedParser.parse(new URL(rss[feed_index]));
					int items = feed.getItemCount();
					
					for (int i = 0; i < items; i++) {
						try {
							FeedItem item = feed.getItem(i);
							ResultSet item_rs;
							
							String title = getMysqlRealScapeString (item.getTitle());
							String url = getMysqlRealScapeString (item.getLink().toString());
							String description = getMysqlRealScapeString (item.getDescriptionAsHTML());
							
							item_rs = stmt2.executeQuery ("SELECT COUNT(*)  FROM `reader_cache` WHERE `url` = '" + url + "' AND `USER_ID` = '" + userid[feed_index] + "'");
							item_rs.next();
							int count = item_rs.getInt("COUNT(*)");
							
							if (count == 0) {
								stmt2.executeUpdate ("INSERT INTO `sarah`.`reader_cache` (`USER_ID`, `FEED_ID`, `feed_name`, `label`, `url`, `title`, `description`) VALUES ('" + userid[feed_index] + "', '" + feedid[feed_index] + "', '" + feed_name[feed_index] + "', '" + label[feed_index] + "', '" + url + "', '" + title + "', '" + description + "');");
								
								if (isVerbose) {
									System.out.print ("+");
								}
								number_of_new_items++;
							} else {
								if (isVerbose) {
									System.out.print ("-");
								}
								number_of_existing_items++;
							}
							if (isVerbose) {
								System.out.println (title);
							}
							number_of_items++;
						} catch (Exception e) {
							System.out.println ("**ERROR: Inner loop");
						}
					}
					
					if (isVerbose) {
						System.out.println ("--");
					}
				} catch (Exception e) {
					System.out.println ("**ERROR: Outer loop");
				}
			}
			
			conn.close();
			
			System.out.println ("    Number of feeds: " + number_of_feeds);
			System.out.println ("    Number of items: " + number_of_items);
			System.out.println ("                New: " + number_of_new_items);
			System.out.println ("           Existing: " + number_of_existing_items);
		} catch (Exception e) {
			sqlError (e, sql);
		}
	}
	
	// taken from http://mashfiqur.blogspot.ca/2013/06/mysql-real-escape-string-in-java.html on 20140601 at 17:45 EST
	public static String getMysqlRealScapeString (String str) {
		String data = null;
		
		if (str != null && str.length() > 0) {
			str = str.replace("\\", "\\\\");
			str = str.replace("'", "\\'");
			str = str.replace("\0", "\\0");
			str = str.replace("\n", "\\n");
			str = str.replace("\r", "\\r");
			str = str.replace("\"", "\\\"");
			str = str.replace("\\x1a", "\\Z");
			data = str;
		}
		
		return data;
	}
}
