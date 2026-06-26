@echo off
setlocal enabledelayedexpansion

:: =====================================================
::  XLRB Phase 2 - Aggressive Orphan Cleanup
::  Includes: Entire Folders + More Views + Services
:: =====================================================

set "PROJECT_ROOT=%~dp0"
set "BACKUP_DIR=%PROJECT_ROOT%cleanup_phase2_backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "LOG_FILE=%BACKUP_DIR%\phase2_cleanup_log.txt"

echo.
echo =====================================================
echo   XLRB Phase 2 - Aggressive Cleanup
echo   This will remove entire legacy folders + old views
echo   Backup will be created at: %BACKUP_DIR%
echo =====================================================
echo.

:: Create backup structure
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "%BACKUP_DIR%\controllers" mkdir "%BACKUP_DIR%\controllers"
if not exist "%BACKUP_DIR%\models" mkdir "%BACKUP_DIR%\models"
if not exist "%BACKUP_DIR%\services" mkdir "%BACKUP_DIR%\services"
if not exist "%BACKUP_DIR%\views" mkdir "%BACKUP_DIR%\views"
if not exist "%BACKUP_DIR%\other" mkdir "%BACKUP_DIR%\other"

echo Phase 2 Cleanup started at %date% %time% > "%LOG_FILE%"
echo. >> "%LOG_FILE%"

set /a MOVED_COUNT=0
set /a SKIPPED_COUNT=0

echo [1] Moving remaining legacy controllers (if any left)...
echo.

call :MoveController "ApprovalHierarchyCrudController.php"
call :MoveController "EmpPostAssignmentCrudController.php"
call :MoveController "GraphEdgeCrudController.php"
call :MoveController "GraphNodeCrudController.php"
call :MoveController "PostCrudController.php"
call :MoveController "PostPermissionCrudController.php"
call :MoveController "PostReportingCrudController.php"

echo.
echo [2] Moving more orphan models (if exist)...
echo.

call :MoveModel "Admin\UserDataScope.php"
call :MoveModel "Admin\EmpPostAssignment.php"
call :MoveModel "IAM\Post.php"
call :MoveModel "IAM\PostReporting.php"

echo.
echo [3] Moving additional orphan services...
echo.

call :MoveService "BookingStateService.php"
call :MoveService "FirebaseService.php"
call :MoveService "OtpNotificationService.php"
call :MoveService "Vehicle\AccessoryImportService_backup.php"

echo.
echo [4] Removing ENTIRE legacy view folders...
echo.

call :MoveEntireViewFolder "admin\post"
call :MoveEntireViewFolder "admin\role"
call :MoveEntireViewFolder "admin\graph-node"
call :MoveEntireViewFolder "admin\graph-edge"
call :MoveEntireViewFolder "admin\approval-hierarchy"
call :MoveEntireViewFolder "admin\reporting-hierarchy"
call :MoveEntireViewFolder "admin\process"
call :MoveEntireViewFolder "admin\user-type"
call :MoveEntireViewFolder "admin\modules"
call :MoveEntireViewFolder "admin\emp-post-assignment"
call :MoveEntireViewFolder "admin\post-permission"
call :MoveEntireViewFolder "admin\post-reporting"

echo.
echo [5] Moving old/duplicate booking views...
echo.

call :MoveViewFile "admin\booking\finance-editold.blade.php"
call :MoveViewFile "admin\booking\oldpendedit.blade.php"
call :MoveViewFile "admin\booking\list1.blade.php"
call :MoveViewFile "admin\hr\temp2copy"

echo.
echo [6] Cleaning other orphan files...
echo.

call :MoveOther "app\Jobs\SendHistoryNotification.php"

echo.
echo =====================================================
echo   PHASE 2 CLEANUP COMPLETED
echo =====================================================
echo   Files/Folders Moved : %MOVED_COUNT%
echo   Files Skipped       : %SKIPPED_COUNT%
echo.
echo   Backup Location : %BACKUP_DIR%
echo   Log File        : %LOG_FILE%
echo =====================================================
echo.

echo Phase 2 finished at %date% %time% >> "%LOG_FILE%"
echo Total Moved: %MOVED_COUNT% ^| Skipped: %SKIPPED_COUNT% >> "%LOG_FILE%"

pause
goto :eof

:: =====================================================
:: FUNCTIONS
:: =====================================================

:MoveController
set "FILE=%~1"
set "SRC=%PROJECT_ROOT%app\Http\Controllers\Admin\%FILE%"
if exist "%SRC%" (
    move "%SRC%" "%BACKUP_DIR%\controllers\%FILE%" >nul
    echo [MOVED] Controller: %FILE%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] Controller not found: %FILE%
    set /a SKIPPED_COUNT+=1
)
goto :eof

:MoveModel
set "FILE=%~1"
set "SRC=%PROJECT_ROOT%app\Models\%FILE%"
if exist "%SRC%" (
    move "%SRC%" "%BACKUP_DIR%\models\%~nx1" >nul
    echo [MOVED] Model: %FILE%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] Model not found: %FILE%
    set /a SKIPPED_COUNT+=1
)
goto :eof

:MoveService
set "FILE=%~1"
set "SRC=%PROJECT_ROOT%app\Services\%FILE%"
if exist "%SRC%" (
    move "%SRC%" "%BACKUP_DIR%\services\%FILE%" >nul
    echo [MOVED] Service: %FILE%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] Service not found: %FILE%
    set /a SKIPPED_COUNT+=1
)
goto :eof

:MoveEntireViewFolder
set "FOLDER=%~1"
set "SRC=%PROJECT_ROOT%resources\views\%FOLDER%"
if exist "%SRC%" (
    xcopy "%SRC%" "%BACKUP_DIR%\views\%~n1\" /E /I /Y >nul
    rmdir /S /Q "%SRC%"
    echo [MOVED] Entire Folder: %FOLDER%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] View folder not found: %FOLDER%
    set /a SKIPPED_COUNT+=1
)
goto :eof

:MoveViewFile
set "FILE=%~1"
set "SRC=%PROJECT_ROOT%resources\views\%FILE%"
if exist "%SRC%" (
    move "%SRC%" "%BACKUP_DIR%\views\%~nx1" >nul
    echo [MOVED] View: %FILE%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] View not found: %FILE%
    set /a SKIPPED_COUNT+=1
)
goto :eof

:MoveOther
set "FILE=%~1"
set "SRC=%PROJECT_ROOT%%FILE%"
if exist "%SRC%" (
    move "%SRC%" "%BACKUP_DIR%\other\%~nx1" >nul
    echo [MOVED] Other: %FILE%
    set /a MOVED_COUNT+=1
) else (
    echo [SKIPPED] File not found: %FILE%
    set /a SKIPPED_COUNT+=1
)
goto :eof