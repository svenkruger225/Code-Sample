DB_FILES=$(git diff --name-only HEAD~1..HEAD specs/sql-updater/)
if [[ -z "$(echo $DB_FILES)" ]]; then
        echo "db files not found"
else
        echo "db files found"
fi