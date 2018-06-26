<?

class City {
    function find($what) {
        global $DB;
        return $DB->getAll("SELECT cities.id, cities.name as name, countries.id as cid, countries.name as country FROM cities INNER JOIN countries ON cities.parent_id = countries.id WHERE cities.name like '$what%' ORDER BY cities.name limit 15");
    }
}

?>