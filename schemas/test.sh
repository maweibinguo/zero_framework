#/bin/bash
function test()  
{  
    echo "arg1 = $1"  
    if [ $1 = "1" ] ;then  
        return 1  
    else  
        return 0  
    fi  
} 

a=`test 3`
echo $?
