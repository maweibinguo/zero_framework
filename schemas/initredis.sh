#/bin/bash

#校验指定的key是否在保留key当中
function in_array()
{
    local need_checked_value=$1
    local result=0
    local remain_keys=("admin:user:1:detail" "admin:user:number" "admin:user:zsort:list" "user:zsort:list" "user:1:detail" "user:number")
    for key_name in ${remain_keys[*]};do
        if [ $key_name = $need_checked_value ];then
            result=1
        fi
    done
    return $result
}

#删除不在保留key的key
for key_name in `/usr/bin/redis-cli keys '*'`;do
    `in_array ${key_name}`
    if [ $? = '0' ]; then
        /usr/bin/redis-cli del $key_name
        if [ $? = '1' ];then
            echo -e "$key_name remove success\r\n"
        else
            echo -e "$key_name remove failed\r\n"
        fi
    fi
done
