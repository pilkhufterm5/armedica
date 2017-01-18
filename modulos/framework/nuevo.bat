:PROMPT

SET /P GET_DIR=What is the name of project?

print "%GET_DIR%"

cd framework

yiic webapp ../"%GET_DIR%"

pause