# -*- coding: UTF-8 -*-
#£¡/usr/bin/env python
import warnings
warnings.filterwarnings("ignore")

import os

def movname(filePath):
    dirlist=[] 
    for fpath, dirname, fnames in os.walk(filePath):
        dirlist.append(fpath)# 所有的文件夹路径     
    return dirlist
def remov(path):
    filename1 =''
    templist= os.listdir(path)
    nlist=[]
    name_list= ['AVI','RMVB','WMV','MOV','MP4','MKV','FLV','TS','WEBM','avi','rmvb','wmv','mov','mp4','mkv','flv','ts','webm']
    for r in templist:
        if any(name in r for name in name_list):(filename1, extension1) = os.path.splitext(r)
    if  filename1:
        for r in templist:
            if filename1 not in r and 'Backdrop.'   not in r:nlist.append(r)
        for r in nlist:
            (filename2, extension2) = os.path.splitext(r)
            os.rename(path+ '/' +filename2+extension2, path + '/' + filename1+extension2)        


path = '/new/JAV_output/'  # 查找文件的路径
dirlist = movname(path)
for i in dirlist:
    remov(i)
