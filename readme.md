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
缺陷1:没有300开头的数据
缺陷2：缺少按价格提醒
缺陷3：rec下的再搜索