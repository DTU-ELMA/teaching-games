import random, csv
import numpy as np
import matplotlib.pyplot as plt
from itertools import chain,cycle,islice

def roundrobin(*iterables):
    "roundrobin('ABC', 'D', 'EF') --> A D E B F C"
    # Recipe credited to George Sakkis
    pending = len(iterables)
    nexts = cycle(iter(it).next for it in iterables)
    while pending:
        try:
            for next in nexts:
                yield next()
        except StopIteration:
            pending -= 1
            nexts = cycle(islice(nexts, pending))

def steppify(x,y):
    sx = roundrobin(chain([0],x),x)
    sy = roundrobin(y,chain(y,[y[-1]]))
    return list(sx), list(sy)


class Market:
    def __init__(self,bidfile = '../php/bids.txt'):
        self.players = {}
        self._playerlist = set()
        self.bidfile = bidfile

    def load_latest_bids(self):
        for ID,name,bid in self.readfile():
            if ID in self._playerlist:
                self.players[ID].setbid(float(bid))
        self.schedule_production()
        for p in self.players.itervalues():
            p.push_bid_and_profit(self.get_current_pay_as_bid_price())
            self.papricelist.append(self.get_current_pay_as_bid_price())
        self.write_stats_file()

    def load_first_bids(self):
        for ID,name,bid in self.readfile():
            self.players[ID] = Player(ID,name)
            self.players[ID].setbid(float(bid))
            self._playerlist.add(ID)
        self.demand = 8*len(self._playerlist)
        self.schedule_production()
        for p in self.players.itervalues():
            p.push_bid_and_profit(self.get_current_pay_as_bid_price())
        self.papricelist = [self.get_current_pay_as_bid_price()]
        self.write_stats_file()

    def readfile(self):
        return csv.reader(open(self.bidfile))

    def schedule_production(self):
        x = 0.0
        pids = {pid:self.players[pid].curbid for pid in self._playerlist}
        pids = sorted(pids.keys(), key=pids.get)
        for pid in pids:
            x+= self.players[pid].curprod
            if x < self.demand:
                self.players[pid].schedprod = self.players[pid].curprod
            else:
                self.players[pid].schedprod = max(0.0,self.demand + self.players[pid].curprod - x)
        

    def get_current_pay_as_bid_price(self):
        x = self.demand
        pids = {pid:self.players[pid].curbid for pid in self._playerlist}
        pids = sorted(pids.keys(), key=pids.get)
        for pid in pids:
            x-= self.players[pid].curprod
            if x < 0:
                return self.players[pid].curbid
        return 100.00

    def get_current_mc_price(self):
        x = self.demand
        pids = {pid:self.players[pid].curbid for pid in self._playerlist}
        pids = sorted(pids.keys(), key=pids.get)
        for pid in pids:
            x-= self.players[pid].curprod
            if x < 0:
                return self.players[pid].mc
        return 100.00

    def plot(self):
        plt.ion()
        plt.figure(1)
        plt.subplot(121)
        plt.cla()
        self.plot_bid_curve()
        plt.subplot(122)
        plt.cla()
        self.plot_profits()
#        plt.figure(2)
#        plt.clf()
#        self.plot_mc_curve()

    def plot_bid_curve(self):
        pids = {pid:self.players[pid].curbid for pid in self._playerlist}
        pids = sorted(pids.keys(), key=pids.get)
        ymc = [self.players[pid].mc for pid in pids]+[100]
        ybid = [self.players[pid].curbid for pid in pids]+[100]
        x = np.cumsum([self.players[pid].curprod for pid in pids]+[self.demand])
        sx,symc = steppify(x,ymc)
        sx,sybid = steppify(x,ybid)
        tmp = [(xx,yy,zz) for xx,yy,zz in zip(sx,sybid,symc) if xx < self.demand]
        tmp.append((self.demand,tmp[-1][1],tmp[-1][2]))
        sxless,sybidless,symcless = zip(*tmp)
        plt.fill_between(sxless,symcless,sybidless,color = 'g',alpha=0.3)
        plt.plot(sx,symc,lw=3,c='k')
        plt.plot(sx,sybid,lw=3,c='k')
        plt.axvline(self.demand,lw=3,ls='--',c='k')
        plt.axhline(sybidless[-1],lw=3,ls='..',c='k')
        plt.title('Final price: {:.02f}'.format(sybidless[-1]))

    def plot_mc_curve(self):
        pids = {pid:self.players[pid].mc for pid in self._playerlist}
        pids = sorted(pids.keys(), key=pids.get)
        ymc = [self.players[pid].mc for pid in pids]+[100]
        ybid = [self.players[pid].curbid for pid in pids]+[100]
        x = np.cumsum([self.players[pid].curprod for pid in pids]+[self.demand])
        sx,symc = steppify(x,ymc)
        sx,sybid = steppify(x,ybid)
        tmp = [(xx,yy,zz) for xx,yy,zz in zip(sx,sybid,symc) if xx < self.demand]
        tmp.append((self.demand,tmp[-1][1],tmp[-1][2]))
        sxless,sybidless,symcless = zip(*tmp)
        plt.fill_between(sxless,symcless,symcless[-1],color = 'g',alpha=0.3)
        plt.plot(sx,symc,lw=3,c='k')
        plt.plot(sx,sybid,lw=3,c='k')
        plt.axvline(self.demand,lw=3,ls='--',c='k')
        plt.axhline(sybidless[-1],lw=3,ls=':',c='k')
        plt.title('Final price: {:.02f}'.format(symcless[-1]))
        plt.plot


    def plot_profits(self):
        for p in self.players.itervalues():
            plt.plot(np.cumsum(p.pabprofitlist),c='k',marker='.')
            plt.plot(np.cumsum(p.mcprofitlist),c='r',marker='.')
        bestprofit = -100.0
        for p in self.players.itervalues():
            if sum(p.pabprofitlist) > bestprofit:
                bestprofit = sum(p.pabprofitlist)
                bestname = p.name
        plt.title('Current leader: {0} with a profit of {1:.01f}'.format(bestname, bestprofit))

    def write_stats_file(self):
        outArr = []
        for pid,p in self.players.iteritems():
            outArr.append(map(float,[p.ID,p.curbid,p.curprod,p.schedprod,p.pabprofitlist[-1]]))
        np.savetxt('../php/stats.txt',outArr,fmt='%d,%.02f,%.02f,%.02f,%.02f')



class Player:
    def __init__(self, ID = -1,name=''):
        self.ID = ID
        self.name = name
        self.mc = round((int(ID) * 10.0)/30000 + 5,2)
        self.bidlist = []
        self.pabprofitlist = []
        self.mcprofitlist = []
        self.prodlist = []
        self.totalprod = 0
    def setbid(self, bid):
        self.curbid = bid
        self.curprod = random.randint(1,3)*5
        self.schedprod = 0.0
    def push_bid_and_profit(self,price = 0.0):
        self.bidlist.append(self.curbid)
        self.pabprofitlist.append((self.curbid-self.mc)*self.schedprod)
        self.mcprofitlist.append((price-self.mc)*self.schedprod)
        self.totalprod += self.schedprod
        self.prodlist.append(self.schedprod)
