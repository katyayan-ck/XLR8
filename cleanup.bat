@echo off
setlocal enabledelayedexpansion

:: =====================================================
::  XLRB Full Orphan Cleanup v2 (Verbose + Safe)
:: =====================================================

set "PROJECT_ROOT=%~dp0"
set "BACKUP_DIR=%PROJECT_ROOT%cleanup_backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "LOG_FILE=%BACKUP_DIR%\cleanup_log.txt"

echo.
echo =====================================================
echo   XLRB Full Orphan Cleanup v2
echo   Project: %PROJECT_ROOT%
echo   Backup Folder: %BACKUP_DIR%
echo =====================================================
echo.

:: Create backup structure
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "%BACKUP_DIR%\controllers" mkdir "%BACKUP_DIR%\controllers"
if not exist "%BACKUP_DIR%\models" mkdir "%BACKUP_DIR%\models"
if not exist "%BACKUP_DIR%\services" mkdir "%BACKUP_DIR%\services"
if not exist "%BACKUP_DIR%\imports" mkdir "%BACKUP_DIR%\imports"
if not exist "%BACKUP_DIR%\exports" mkdir "%BACKUP_DIR%\exports"
if not exist "%BACKUP_DIR%\views" mkdir "%BACKUP_DIR%\views"

echo Cleanup started at %date% %time% > "%LOG_FILE%"
echo. >> "%LOG_FILE%"

set /a MOVED_COUNT=0
set /a SKIPPED_COUNT=0
set /a NOTFOUND_COUNT=0

echo [1] Processing Controllers...
echo.

call :ProcessController "DashboardController-090526.bak.php"
call :ProcessController "DashboardController-100526Bak.php"
call :ProcessController "ApprovalHierarchyCrudController.php"
call :ProcessController "DashboardControllerCrudController.php"
call :ProcessController "EmpPostAssignmentCrudController.php"
call :ProcessController "GraphEdgeCrudController.php"
call :ProcessController "GraphNodeCrudController.php"
call :ProcessController "ModulesCrudController.php"
call :ProcessController "PostCrudController.php"
call :ProcessController "PostPermissionCrudController.php"
call :ProcessController "PostReportingCrudController.php"
call :ProcessController "ProcessCrudController.php"
call :ProcessController "ReportingHierarchyCrudController.php"
call :ProcessController "RoleCrudController.php"
call :ProcessController "UserTypeCrudController.php"

echo.
echo [2] Processing Models...
echo.

call :ProcessModel "Admin\UserDataScope.php"
call :ProcessModel "Admin\EmpPostAssignment.php"
call :ProcessModel "IAM\Post.php"
call :ProcessModel "IAM\PostReporting.php"
call :ProcessModel "Core\GraphEdge.php"
call :ProcessModel "Core\GraphNode.php"
call :ProcessModel "Core\ApprovalHierarchy.php"

echo.
echo [3] Processing Services...
echo.

call :ProcessService "Importers\UserImporter.php"
call :ProcessService "Exporters\UserExporter.php"
call :ProcessService "ApprovalService.php"
call :ProcessService "HR\UserReportingService.php"

echo.
echo [4] Processing Imports ^& Exports...
echo.

call :ProcessImport "UserImporter.php"
call :ProcessExport "VehicleDataExport.php"

echo.
echo [5] Processing Temp View Folders...
echo.

call :ProcessViewFolder "admin\temp2copy"
call :ProcessViewFolder "admin\hr\temp2copy"

echo.
echo =====================================================
echo   CLEANUP SUMMARY
echo =====================================================
echo   Files Moved     : %MOVED_COUNT%
echo   Files Skipped   : %SKIPPED_COUNT%
echo   Files Not Found : %NOTFOUND_COUNT%
echo.
echo   Log file saved at: %LOG_FILE%
echo   Backup location  : %BACKUP_DIR%
echo =====================================================
echo.

echo Cleanup finished at %date% %time% >> "%LOG_FILE%"
echo Total Moved: %MOVED_COUNT% ^| Skipped: %SKIPPED_COUNT% ^| Not Found: %NOTFOUND_COUNT% >> "%LOG_FILE%"

pause
goto :eof

:: =====================================================
:: FUNCTIONS
:: =====================================================

:ProcessController
set "FILE=%~1"
set "FULL_PATH=%PROJECT_ROOT%app\Http\Controllers\Admin\%FILE%"

if exist "%FULL_PATH%" (
    move "%FULL_PATH%" "%BACKUP_DIR%\controllers\%FILE%" >nul
    echo [MOVED]    Controller: %FILE%
    echo [MOVED] Controller: %FILE% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] Controller: %FILE%
    echo [NOT FOUND] Controller: %FILE% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof

:ProcessModel
set "FILE=%~1"
set "FULL_PATH=%PROJECT_ROOT%app\Models\%FILE%"

if exist "%FULL_PATH%" (
    move "%FULL_PATH%" "%BACKUP_DIR%\models\%~nx1" >nul
    echo [MOVED]    Model: %FILE%
    echo [MOVED] Model: %FILE% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] Model: %FILE%
    echo [NOT FOUND] Model: %FILE% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof

:ProcessService
set "FILE=%~1"
set "FULL_PATH=%PROJECT_ROOT%app\Services\%FILE%"

if exist "%FULL_PATH%" (
    move "%FULL_PATH%" "%BACKUP_DIR%\services\%~nx1" >nul
    echo [MOVED]    Service: %FILE%
    echo [MOVED] Service: %FILE% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] Service: %FILE%
    echo [NOT FOUND] Service: %FILE% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof

:ProcessImport
set "FILE=%~1"
set "FULL_PATH=%PROJECT_ROOT%app\Imports\%FILE%"

if exist "%FULL_PATH%" (
    move "%FULL_PATH%" "%BACKUP_DIR%\imports\%FILE%" >nul
    echo [MOVED]    Import: %FILE%
    echo [MOVED] Import: %FILE% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] Import: %FILE%
    echo [NOT FOUND] Import: %FILE% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof

:ProcessExport
set "FILE=%~1"
set "FULL_PATH=%PROJECT_ROOT%app\Exports\%FILE%"

if exist "%FULL_PATH%" (
    move "%FULL_PATH%" "%BACKUP_DIR%\exports\%FILE%" >nul
    echo [MOVED]    Export: %FILE%
    echo [MOVED] Export: %FILE% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] Export: %FILE%
    echo [NOT FOUND] Export: %FILE% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof

:ProcessViewFolder
set "FOLDER=%~1"
set "FULL_PATH=%PROJECT_ROOT%resources\views\%FOLDER%"

if exist "%FULL_PATH%" (
    xcopy "%FULL_PATH%" "%BACKUP_DIR%\views\%~n1\" /E /I /Y >nul
    rmdir /S /Q "%FULL_PATH%"
    echo [MOVED]    View Folder: %FOLDER%
    echo [MOVED] View Folder: %FOLDER% >> "%LOG_FILE%"
    set /a MOVED_COUNT+=1
) else (
    echo [NOT FOUND] View Folder: %FOLDER%
    echo [NOT FOUND] View Folder: %FOLDER% >> "%LOG_FILE%"
    set /a NOTFOUND_COUNT+=1
)
goto :eof