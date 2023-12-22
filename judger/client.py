# 导入requests模块
import requests
# 导入json模块
import json
# 导入hashlib模块
import hashlib
# 导入time模块
import time
# 导入os模块
import os
import subprocess
from urllib.parse import quote
import psutil
import shutil

# 定义主机IP地址
hostip = "http://127.0.0.1/"


def getMemSize(pid):
    # 根据进程号来获取进程的内存大小
    process = psutil.Process(pid)
    memInfo = process.memory_info()
    return memInfo.rss / 1024 / 1024


def getnowkey():  # 获取密钥
    # 获取当前时间
    nowtime = round(time.time())
    strkey = str(round(nowtime/10))+"zsv"
    m = hashlib.md5()
    m.update(strkey.encode('utf-8'))
    md5key = m.hexdigest()
    return md5key


def file_put_contents(path, text):
    ans = open(path, 'w')
    ans.write(text)
    ans.close()


def file_get_contents(path):
    ans = open(path, 'r')
    text = ans.read()
    ans.close()
    return text


def getSubmissionUrl(id):
    return hostip+'judgedetial?pass=' + getnowkey()+"&submissionid="+id


def getJudgement(sub):
    raw = requests.get(getSubmissionUrl(sub)).text
    # 尝试次数
    trycnt = 0
    # 如果获取的数据为空，则重试3次
    while (raw == ""):
        trycnt += 1
        if trycnt > 3:
            print("获取数据失败，跳过")
            return 0
        print("错误：空数据包，1S后重试"+str(trycnt))
        time.sleep(1)
        raw = requests.get(getSubmissionUrl(sub)).text
    # 将获取的数据转换为json格式
    thissubmit = json.loads(raw)
    # 打印获取的数据
    # print(thissubmit)
    if (thissubmit['method'] == "SKIP"):
        return 0
    return thissubmit


def monitor_process(process, dataid, max_memory=128, max_time=500):
    start_time = time.time()
    max_memory_mb = max_memory
    memory_info=0
    current_time=0
    while True:
        # 检查子进程是否完成
        if process.poll() is not None:
            break

        # 检查内存占用
        memory_info = getMemSize(process.pid)

        if memory_info > max_memory_mb:
            print(f"Memory limit exceeded for data {dataid}")
            process.terminate()
            return f"MLE!Mem:{memory_info}MB"
        # 检查运行时间
        current_time = time.time()
        #print(f"mem:{memory_info}MB,time:{current_time - start_time}")
        if current_time - start_time > max_time/1000:
            print(f"Time limit exceeded for data {dataid}")
            process.terminate()
            return f"TLE!Time:{current_time - start_time} s"
        time.sleep(0.05)
    return f"S!M:{memory_info}MB,T:{current_time - start_time} s"


def judgeOne(judgedata):
    os.mkdir("ctd")
    thissubmit = judgedata
    errs = ''
    if (thissubmit == {}):
        return {'status': 'JudgementEmpty', 'score': 0}
    totalsco = 0
    file_put_contents("ctd/ans.cpp", thissubmit['ans'])
    dataid = 0
    # 编译ans.cpp，生成ans.exe
    comps = os.system("cd ctd & g++ ans.cpp -o ans.exe 2> c.err >c.out")
    # 如果编译失败，则打印CE
    if (comps != 0):
        return {'status': 'CE', 'err': file_get_contents("ctd/c.err"),  'score': 0}
    # 否则，遍历获取的数据，写入文件
    else:
        
        # 遍历thissubmit中的data，将in文件写入ctd/dataid.in，将out文件写入ctd/dataid.out，并执行ans.exe
        for thisdata in thissubmit['data']:
            file_put_contents("ctd/"+str(dataid)+".in", thisdata['in'])

            # file_put_contents("ctd/"+str(dataid)+".stdout", thisdata['out'])

            file_put_contents("ctd/"+str(dataid)+".out", '')

            # 创建一个子进程，使用"cd data & ans.exe < "+str(dataid) +".in > "+str(dataid)+".out"
            # 创建子进程
            process = subprocess.Popen(
                'ctd/ans.exe',
                stdin=open("ctd/"+str(dataid) + ".in", "r"),
                stdout=open("ctd/"+str(dataid) + ".out", "w"),
                stderr=open("ctd/"+str(dataid) + ".err", "w")
            )

            # 等待子进程完成
            res = monitor_process(
                process, dataid, thissubmit['mem'], thissubmit['time'])
            process.terminate()
            print("#"+str(dataid)+res)
            errs += res+' -#'+str(dataid)+'\n'
            if(res[0]!='S'):
                dataid += 1
                continue
            # 对比输出结果(文件：str(dataid)+".out")与标准结果（thisdata['out'])是否一致
            # 获取输出结果，忽略行末空格
            thisdata['out'] = thisdata['out'].strip()
            userout = file_get_contents("ctd/"+str(dataid)+".out").strip()
            if (userout == thisdata['out']):
                # print(thisdata)
                totalsco += 100
            else:
                pass
                # print("WA:"+str(dataid)+"user:"+userout+"ans:"+thisdata['out'])
            dataid += 1

    totalsco = round(totalsco/dataid)
    if (totalsco >= 100):
        stau = 'AC'
    elif (totalsco <= 0):
        stau = 'AE'
    else:
        stau = "UAC"
    return {'status': stau, 'err': 'AC '+str(round(totalsco*dataid/100))+" of "+str(dataid)+'\n'+errs, 'score': totalsco}


def mainF():
    listraw = requests.get(hostip+'judgequeue?pass='+getnowkey()).text
    if (listraw == ''):
        # print("无评测任务或校验失败")
        return 0
    judgequeue = json.loads(listraw)
    if (judgequeue == []):
        return 0
    # 打印评测队列
    print("评测队列:")
    print(judgequeue)
    # 遍历评测队列
    for sub in judgequeue:
        # 打印正在评测的子任务
        print("正在评测："+sub)
        # 获取评测详情
        thissubmit = getJudgement(sub)
        if (thissubmit == 0):
            print(sub+"评测取消")
            continue
        else:
            print(sub+"数据获取成功，开始评测:")
            res = judgeOne(thissubmit)
            shutil.rmtree("ctd")
            #print(res)
            savurl = hostip+'judgeres?pass='+getnowkey()+"&status=" +\
                res['status'] +\
                "&err=" +\
                quote(res['err']) +\
                "&score=" +str(res['score'])+\
                "&sid="+sub
            saveres = requests.get(savurl).text
            print(saveres)
        # 获取评测队列


print("Juger开始运行，每秒查询一次评测记录")
while True:
    if os.path.exists("ctd"):
        shutil.rmtree("ctd")
    mainF()
    # break
    time.sleep(1)
