@echo off
setlocal EnableDelayedExpansion

REM --- CONFIGURATION ---
set "outputFile=_AllCodeCombined.txt"
set "extensions=*.html *.php *.json *.js *.css"
REM ---------------------

REM Delete old file if it exists
if exist "%outputFile%" del "%outputFile%"

echo -------------------------------------------------------
echo  SCANNING THIS FOLDER AND ALL SUB-FOLDERS...
echo -------------------------------------------------------

REM Loop through all files recursively
for /r %%f in (%extensions%) do (
    
    REM Check to ensure we don't process the output file itself
    if /i not "%%~nxf"=="%outputFile%" (
        
        echo Processing: "%%f"
        
        REM Write a header to the output file
        (
            echo.
            echo ==============================================================================
            echo FILE PATH: %%f
            echo ==============================================================================
            echo.
        ) >> "%outputFile%"

        REM Write the content of the file
        type "%%f" >> "%outputFile%"
        
        REM Add spacing
        echo. >> "%outputFile%"
        echo. >> "%outputFile%"
    )
)

echo.
echo -------------------------------------------------------
echo  DONE!
echo  All code has been saved to: %outputFile%
echo -------------------------------------------------------
pause