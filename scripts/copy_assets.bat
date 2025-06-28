@echo off
echo Starting Dason Assets Copy Process...

REM Create destination directories
mkdir "public\assets\css" 2>nul
mkdir "public\assets\js" 2>nul
mkdir "public\assets\images" 2>nul
mkdir "public\assets\fonts" 2>nul
mkdir "public\assets\libs" 2>nul

echo Copying CSS files...
xcopy "Dason-Laravel_v1.0.0\Admin\public\assets\css\*" "public\assets\css\" /E /Y /I

echo Copying JS files...
xcopy "Dason-Laravel_v1.0.0\Admin\public\assets\js\*" "public\assets\js\" /E /Y /I

echo Copying Images...
xcopy "Dason-Laravel_v1.0.0\Admin\public\assets\images\*" "public\assets\images\" /E /Y /I

echo Copying Fonts...
xcopy "Dason-Laravel_v1.0.0\Admin\public\assets\fonts\*" "public\assets\fonts\" /E /Y /I

echo Copying Libraries...
xcopy "Dason-Laravel_v1.0.0\Admin\public\assets\libs\*" "public\assets\libs\" /E /Y /I

echo Dason Assets Copy Completed!
echo.
echo Next steps:
echo 1. Update package.json
echo 2. Run: npm install
echo 3. Run: npm run dev
echo 4. Test at /admin/dason

pause
