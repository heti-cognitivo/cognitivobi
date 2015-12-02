<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcBalanceGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE PROCEDURE BalanceGeneral(IN dates varchar(100))
BEGIN
SET SQL_SAFE_UPDATES = 0;
CREATE TEMPORARY TABLE IF NOT EXISTS BalanceGeneralTable
	(RowID int not null primary key AUTO_INCREMENT,
	id_chart int,
	parent_id_chart int,
	code VarChar(50),
	name varchar(50),
    trans_date DATETIME,
    Saldo float);

SET @stmt = "INSERT into BalanceGeneralTable (id_chart,parent_id_chart,code,name,trans_date,Saldo)
select accounting_chart.id_chart,
accounting_chart.parent_id_chart,
accounting_chart.code,
accounting_chart.name,
accounting_journal_detail.trans_date,
SUM(accounting_journal_detail.credit-accounting_journal_detail.debit) as saldo
from accounting_chart
INNER JOIN accounting_journal_detail
ON accounting_chart.id_chart = accounting_journal_detail.id_chart";

IF (dates IS NOT NULL) THEN
	SET @stmt = CONCAT(@stmt,dates);
END IF;

SET @stmt = CONCAT(@stmt," group by accounting_chart.code;");
PREPARE BalanceGeneralQry FROM @stmt;
EXECUTE BalanceGeneralQry;

SELECT ROW_COUNT() INTO @RowsToProcess;
SET @CurrentRow=0;
WHILE @CurrentRow<@RowsToProcess
DO
    SET @CurrentRow=@CurrentRow+1;
SELECT
    id_chart, saldo, parent_id_chart, code
INTO @id_chart , @saldo , @parent_id_chart , @code FROM
    BalanceGeneralTable
WHERE
    RowID = @CurrentRow;
	WHILE @parent_id_chart IS NOT NULL
	DO
		SELECT COUNT(*) INTO @CheckExists FROM BalanceGeneralTable WHERE id_chart = @parent_id_chart;
		IF (@CheckExists = 0) THEN
			INSERT INTO BalanceGeneralTable(Saldo,code,id_chart,name,parent_id_chart)
			SELECT @Saldo,code,id_chart,name,parent_id_chart
			FROM accounting_chart
			WHERE id_chart = @parent_id_chart;
		ELSE
			UPDATE BalanceGeneralTable SET saldo = @Saldo + saldo WHERE id_chart = @parent_id_chart;
        END IF;
		SET @id_chart = @parent_id_chart;
		SELECT
    parent_id_chart
INTO @parent_id_chart FROM
    accounting_chart
WHERE
    id_chart = @id_chart;


	END WHILE;
END WHILE;

SELECT
    code,name,trans_date,saldo
FROM
    BalanceGeneralTable
WHERE
    SALDO <> 0
ORDER BY code;
SET SQL_SAFE_UPDATES = 1;
END;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS BalanceGeneral;');
    }
}
