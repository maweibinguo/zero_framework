<?php
namespace core\base;

class Attach extends Components
{

    /**
     * 获取附件根目录
     *
     * 存储路径格式：/根目录/服务名/项目名/年/月/日/文件名
     * 例如：/www/service/attachment/cbcreport/article/2010/05/21/filename.jpg
     * $itemName 是附件类型名称，如target、archive
     * 本方法仅返回：/www/cbcreport/data
     *
     * @access public
     * @param $itemName
     * @return string
     */
    public function getRootPath()
    {
        return ATTACH_ROOT;
    }

    /**
     * 获取附件路径
     *
     * 存储路径格式：/根目录/服务名/项目名/年/月/日/文件名
     * 例如：/www/service/attachment/cbcreport/article/2011/02/21/filename.jpg
     * 本方法仅返回：article/2011/02/21
     *
     * @param string $itemName
     * @return string
     */
    public function getFilePath($itemName)
    {
        //root path
        $rootPath = strtolower($this->getRootPath());
        if (!is_dir($rootPath)){
            mkdir($rootPath, 0755);
        }
        
        //item
        $itemName = strtolower($itemName);
        $itemPath = $rootPath . $itemName;
        if (!is_dir($itemPath)){
            mkdir($itemPath, 0755);
        }
        //date
        $filePath = $itemPath . '/' . date('Y');
        if (!is_dir($filePath)){
            mkdir($filePath, 0755);
        }
        $filePath = $filePath . '/' . date('m');
        if (!is_dir($filePath)){
            mkdir($filePath, 0755);
        }
        $filePath = $filePath . '/' . date('d');
        if (!is_dir($filePath)){
            mkdir($filePath, 0755);
        }
        //把$rootPath中的内容替换成 ‘’
        return $filePath;
    }

    /**
     * 下载远程文件并保存
     *
     * @param string $fileUrl
     * @param string $itemName
     * @param string $fileName
     * @return string
     */
    public function getRemoteFile($fileUrl, $itemName, $fileName = '', $isReturnUrl = true)
    {
        $rootPath = $this->getRootPath();
        $filePath = $this->getFilePath($itemName);
        if (!$fileName){
            $fileName = $this->getNewFileName($fileUrl);
        }
        
        $content = file_get_contents($fileUrl);
        file_put_contents($rootPath . $filePath . $fileName, $content);
        
        if ($isReturnUrl){
            $domainName = 'http://' . $this->getDomainName();
            return $domainName . $filePath . $fileName;
        }else{
            return $filePath . $fileName;
        }
    }

    /**
     * 获取附件文件名
     *
     * @param string $fileName
     * @return string
     */
    public function getNewFileName($fileName)
    {
        //获取文件扩展名
        $item = explode('.', $fileName);
        $ext = strtolower(end($item));
    
        //新文件名
        list($usecond, $second) = explode(' ', microtime());
        $usecond = $second . substr($usecond, 2, 4);
        $newFileName = $usecond . '.' . $ext;
    
        return $newFileName;
    }

}
