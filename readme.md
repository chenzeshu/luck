1. 2017.10.7：完成了自定义ui-kit分页

2. list()的重构优势
```
   list($data, $pageCount) = $this->dataAffair($page, $size, $wanTime);
```

```
 private function dataAffair($page, $size, $wanTime){
    ...

    return [$data, $pageCount];
}
```

3. 2017.10.8
~~缺陷1:没有300开头的数据~~(从tushare补全)

缺陷2：缺少按价格提醒

缺陷3：rec下的再搜索

缺陷4: 在`weekRepo`121行增加`$this->new = false`的初设值

4. 备忘
部署注意1, .env的session等设置; 2, cron; 3, key:generate