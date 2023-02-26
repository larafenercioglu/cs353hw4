import java.sql.*;

public class DBConnector {
    public static void main(String[] args) {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.out.println("MySQL JDBC Driver not found!");
            e.printStackTrace();
        }

        final String USERNAME = "lara.fenercioglu";
        final String PASSWORD = "YAfSK1bW";
        final String DBNAME = "lara_fenercioglu";
        final String URL = "jdbc:mysql://dijkstra.ug.bcc.bilkent.edu.tr/" + DBNAME;

        System.out.println("Connecting to database");
        Connection connection;
        try {
            connection = DriverManager.getConnection(URL, USERNAME, PASSWORD);
        } catch (SQLException e) {
            System.out.println("Connection failed!");
            e.printStackTrace();
            return;
        }

        System.out.println("Connected successfully");
        Statement stmt;

        try {
            stmt = connection.createStatement();

            // Drop tables if they exist already
            System.out.println("Dropping tables");
            stmt.executeUpdate("DROP TABLE IF EXISTS buy");
            stmt.executeUpdate("DROP TABLE IF EXISTS customer");
            stmt.executeUpdate("DROP TABLE IF EXISTS product");

            // Create tables
            System.out.println("Creating tables");
            stmt.executeUpdate("CREATE TABLE customer(" + "cid CHAR(12)," + "cname VARCHAR(50)," + "bdate DATE,"
                    + "address VARCHAR(50)," + "city VARCHAR(20)," + "wallet FLOAT,"
                    + "PRIMARY KEY(cid))" + "ENGINE=innodb;");

            stmt.executeUpdate("CREATE TABLE product(" + "pid CHAR(8)," + "pname VARCHAR(20)," + "price FLOAT,"
                    + "stock INT," + "PRIMARY KEY (pid))" + "ENGINE=innodb;");

            stmt.executeUpdate("CREATE TABLE buy(" + "cid CHAR(12)," + "pid CHAR(8)," + "quantity INT,"
                    + "PRIMARY KEY (cid, pid)," +
                    "FOREIGN KEY (cid) REFERENCES customer(cid) ON DELETE CASCADE ON UPDATE CASCADE," + "FOREIGN KEY (pid) REFERENCES product(pid) ON DELETE CASCADE ON UPDATE CASCADE)"
                    + "ENGINE=innodb;");

            // Insert values into tables
            System.out.println("Inserting into tables");
            stmt.executeUpdate("INSERT INTO customer VALUES"
                    + "('C101', 'Ali', '1997-3-3', 'Besiktas', 'Istanbul', 114.50),"
                    + "('C102', 'Veli', '2001-5-19', 'Bilkent', 'Ankara', 200.00),"
                    + "('C103', 'Ayse', '1972-4-23', 'Tunali', 'Ankara', 15.00),"
                    + "('C104', 'Alice', '1990-10-29', 'Meltem', 'Antalya', 1024.00),"
                    + "('C105', 'Bob', '1987-8-30', 'Stretford', 'Manchester', 15.00);");

            stmt.executeUpdate("INSERT INTO product VALUES"
                    + "('P101', 'powerbank', 300.00, 2),"
                    + "('P102', 'battery', 5.50, 5),"
                    + "('P103', 'laptop', 3500.00, 10),"
                    + "('P104', 'mirror', 10.75, 50),"
                    + "('P105', 'notebook', 3.85, 100),"
                    + "('P106', 'carpet', 50.99, 1),"
                    + "('P107', 'lawn mower', 1025.00, 3);");

            stmt.executeUpdate("INSERT INTO buy VALUES"
                    + "('C101', 'P105', 2),"
                    + "('C102', 'P105', 2),"
                    + "('C103', 'P105', 5),"
                    + "('C101', 'P101', 1),"
                    + "('C102', 'P102', 4),"
                    + "('C105', 'P104', 1);");
            System.out.println("Inserted values into tables successfully.");

            //Give the birth dates, addresses and cities of the customers who has the
            //minimum amount money in his/her wallet.
            System.out.printf("%12s%12s%12s%n", "bdate", "address", "city");
            ResultSet min_money_customers = stmt.executeQuery("SELECT bdate, address, city FROM customer WHERE wallet = (SELECT MIN(wallet) FROM customer)");
            while (min_money_customers.next()) {
                System.out.printf("%12s%12s%12s%n", min_money_customers.getString("bdate"),
                        min_money_customers.getString("address"), min_money_customers.getString("city"));
            }

            System.out.println("--------------------------------------");
            //Give the names of the customers who bought all products whose price is less
            //than 10.
            System.out.printf("%12s%n", "cname");
            ResultSet customers_price_10 = stmt.executeQuery("SELECT DISTINCT c.cname FROM customer as c WHERE NOT EXISTS (SELECT pid FROM product WHERE price < 10 and pid NOT IN (SELECT b.pid FROM buy as b WHERE b.cid = c.cid));");
            while (customers_price_10.next()) {
                System.out.printf("%12s%n", customers_price_10.getString("cname"));
            }

            System.out.println("--------------------------------------");
            //Give the names of the products who are bought by at least 3 different
            //customers.
            System.out.printf("%12s%n", "pname");
            ResultSet products_3_dif_cust = stmt.executeQuery("SELECT p.pname FROM product as p WHERE p.pid = (SELECT pid FROM buy GROUP BY pid HAVING count(cid) > 2)");
            while (products_3_dif_cust.next()) {
                System.out.printf("%12s%n", products_3_dif_cust.getString("pname"));
            }

            System.out.println("--------------------------------------");
            //Give the names of the products which can be bought by the youngest customer
            //with his/her money in the wallet.
            System.out.printf("%12s%n", "pname");
            ResultSet products_can_cust = stmt.executeQuery("SELECT p.pname FROM product as p, buy as b, customer as c WHERE p.pid = b.pid and c.cid = b.cid and p.price <= c.wallet and c.bdate = (SELECT MAX(bdate) FROM customer)");
            while (products_can_cust.next()) {
                System.out.printf("%12s%n", products_can_cust.getString("pname"));
            }

            System.out.println("--------------------------------------");
            //Give the name of the customer who spent the maximum money.
            System.out.printf("%12s%n", "cname");
            ResultSet cust_max_money_spent = stmt.executeQuery("SELECT tots.name as cname FROM (SELECT SUM(p.price * b.quantity) as tot, c.cname as name FROM customer as c, product as p, buy as b WHERE p.pid = b.pid and c.cid = b.cid GROUP BY c.cid) as tots WHERE tots.tot = (SELECT MAX(tots.tot) as tot FROM (SELECT SUM(p.price * b.quantity) as tot, b.cid as id FROM customer as c, product as p, buy as b WHERE p.pid = b.pid and c.cid = b.cid GROUP BY c.cid) as tots)");
            while (cust_max_money_spent.next()) {
                System.out.printf("%12s%n", cust_max_money_spent.getString("cname"));
            }

        } catch (SQLException e) {
            System.out.println("SQLException: " + e.getMessage());
            e.printStackTrace();
        }
    }
}